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
        window.addEventListener('keyup', function (event) {
            that.onKeyup(event);
        });
        window.addEventListener('paste', function (event) {
            that.onPaste(event);
        });
        window.addEventListener('focus', function () {
            that.onFocus();
        });
        window.addEventListener('blur', function () {
            that.onBlur();
        });
    }

    onKeyup(event) {
        if (event.altKey || event.metaKey || event.ctrlKey) {
            return;
        }

        event.stopPropagation();
        event.preventDefault();

        if (event.key.length === 1) {
            this.commands[this.cursorLine] = this.commands[this.cursorLine].substring(0, this.cursorChar) + event.key + this.commands[this.cursorLine].substring(this.cursorChar);
            this.cursorChar++;
            this.update();
            return;
        }
        if (event.key === "Backspace") {
            if (this.cursorChar === 0) {
                if (this.cursorLine !== 0) {
                    this.cursorLine--;
                    this.cursorChar = this.commands[this.cursorLine].length;
                    this.commands[this.cursorLine] = this.commands[this.cursorLine] + this.commands.splice(this.cursorLine + 1, 1);
                    this.update();
                }
            } else {
                this.commands[this.cursorLine] = this.commands[this.cursorLine].substring(0, this.cursorChar - 1) + this.commands[this.cursorLine].substring(this.cursorChar);
                this.cursorChar--;
                this.update();
            }
            return;
        }
        if (event.key === "Delete") {
            if (this.cursorChar === this.commands[this.cursorLine].length) {
                if (this.cursorLine !== this.commands.length - 1) {
                    this.commands[this.cursorLine] = this.commands[this.cursorLine] + this.commands.splice(this.cursorLine + 1, 1);
                    this.update();
                }
            } else {
                this.commands[this.cursorLine] = this.commands[this.cursorLine].substring(0, this.cursorChar) + this.commands[this.cursorLine].substring(this.cursorChar + 1);
                this.update();
            }
            return;
        }
        if (event.key === "Enter") {
            this.commands.splice(this.cursorLine + 1, 0, this.commands[this.cursorLine].substring(this.cursorChar));
            this.commands[this.cursorLine] = this.commands[this.cursorLine].substr(0, this.cursorChar);
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

    onPaste(event) {
        event.stopPropagation();
        event.preventDefault();

        let input = event.clipboardData.getData("Text");
        input = input.replace(/\r\n/g, "\n").replace(/\r/g, "\n");
        let inputCommands = input.split("\n");

        if (inputCommands.length === 0) {
            return;
        }

        let pre = this.commands.slice(0, this.cursorLine);
        let suf = this.commands.slice(this.cursorLine + 1);

        let curPre = this.commands[this.cursorLine].substr(0, this.cursorChar);
        let curSuf = this.commands[this.cursorLine].substring(this.cursorChar);

        if (inputCommands.length > 1) {
            this.cursorLine += inputCommands.length - 1;
            this.cursorChar = 0;
        }
        this.cursorChar += inputCommands[inputCommands.length - 1].length;

        inputCommands[0] = curPre + inputCommands[0];
        inputCommands[inputCommands.length - 1] = inputCommands[inputCommands.length - 1] + curSuf;

        this.commands = pre.concat(inputCommands).concat(suf);
        this.update();
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
