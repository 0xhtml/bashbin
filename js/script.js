class Bash {
    constructor(elem, commands, startText = "Bash", lineStart = "\nuser@0xhtml:~$ ", cursor = "<span id=\"cursor\"></span>") {
        this.elem = elem;
        this.commands = commands;
        this.startText = startText;
        this.lineStart = lineStart;
        this.cursorVisible = document.hasFocus();
        this.cursor = cursor;
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
                this.update();
            }
            return;
        }
        if (event.key === "ArrowDown") {
            if (this.cursorLine < this.commands.length - 1) {
                this.cursorLine++;
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
            return;
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
        if (this.cursorVisible) {
            tmpCommands[this.cursorLine] = tmpCommands[this.cursorLine].substring(0, this.cursorChar) + this.cursor + tmpCommands[this.cursorLine].substring(this.cursorChar);
        }
        this.elem.innerHTML = this.startText + this.lineStart + tmpCommands.join(this.lineStart);
    }
}
