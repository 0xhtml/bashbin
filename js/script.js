class Bash {
    constructor(elem, commands = [""], lineStart = "") {
        this.elem = elem;
        this.commands = commands;
        this.lineStart = lineStart;
        this.cursorVisible = document.hasFocus();
        this.cursorLine = this.commands.length - 1;
        this.cursorChar = this.commands[this.cursorLine].length;

        this.update();
        let that = this;
        window.addEventListener('keyup', function (e) {
            that.onKeyup(e);
        });
        window.addEventListener('focus', function () {
            that.onFocus();
        });
        window.addEventListener('blur', function () {
            that.onBlur();
        });
    }

    onKeyup(event) {
        if (event.key.length === 1) {
            this.commands[this.cursorLine] = this.commands[this.cursorLine].substring(0, this.cursorChar) + event.key + this.commands[this.cursorLine].substring(this.cursorChar);
            this.cursorChar++;
            this.update();
            return;
        }
        console.log(event.key);
        if (event.key === "Backspace") {
            if (this.cursorChar > 0) {
                this.commands[this.cursorLine] = this.commands[this.cursorLine].substring(0, this.cursorChar - 1) + this.commands[this.cursorLine].substring(this.cursorChar);
                this.cursorChar--;
                this.update();
            }
            return;
        }
        if (event.key === "Enter") {
            this.commands.push("");
            this.cursorLine++;
            this.cursorChar = 0;
            this.update();
            return;
        }
        if (event.key === "ArrowUp") {
            if (this.cursorLine > 0) {
                this.cursorLine--;
                if (this.cursorChar > this.commands[this.cursorLine].length) {
                    this.cursorChar = this.commands[this.cursorLine].length;
                }
                this.update();
            }
            return;
        }
        if (event.key === "ArrowDown") {
            if (this.cursorLine < this.commands.length - 1) {
                this.cursorLine++;
                if (this.cursorChar > this.commands[this.cursorLine].length) {
                    this.cursorChar = this.commands[this.cursorLine].length;
                }
                this.update();
            }
            return;
        }
        if (event.key === "ArrowLeft") {
            if (this.cursorChar > 0) {
                this.cursorChar--;
                this.update();
            }
            return;
        }
        if (event.key === "ArrowRight") {
            if (this.cursorChar < this.commands[this.cursorLine].length) {
                this.cursorChar++;
                this.update();
            }
        }
    }

    onFocus() {
        this.cursorVisible = true;
        this.update();
    }

    onBlur() {
        this.cursorVisible = false;
        this.update();
    }

    update() {
        let tmpCommands = this.commands.slice();
        for (let i = 0; i < tmpCommands.length; i++) {
            if (this.cursorVisible && i === this.cursorLine) {
                tmpCommands[i] = tmpCommands[this.cursorLine].substring(0, this.cursorChar).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;") + "<span id=\"cursor\"></span>" + tmpCommands[this.cursorLine].substring(this.cursorChar).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
            } else {
                tmpCommands[i] = tmpCommands[i].replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
            }
        }
        this.elem.innerHTML = (this.lineStart + tmpCommands.join("\n" + this.lineStart));
    }

    send() {
        let form = document.createElement("form");
        form.setAttribute("method", "post");
        form.setAttribute("action", "?");
        form.setAttribute("hidden", "");

        let textarea = document.createElement("textarea");
        textarea.setAttribute("name", "bash");
        textarea.value = JSON.stringify(this.commands);

        form.appendChild(textarea);
        document.body.appendChild(form);

        form.submit();
    }
}
