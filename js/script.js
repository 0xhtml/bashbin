let cursorVisible = true;

function showCursor() {
    if (!cursorVisible) {
        cursorVisible = true;
        let value = document.getElementById("bash").value;
        document.getElementById("bash").value = value + cursor;
    }
}

function hideCursor() {
    if (cursorVisible) {
        cursorVisible = false;
        let value = document.getElementById("bash").value;
        document.getElementById("bash").value = value.substr(0, value.length - 1);
        setTimeout(function () {
            showCursor();
        }, 500);
    }
}

window.addEventListener("load", function () {
    document.getElementById("bash").value = document.getElementById("bash").innerHTML;
    setTimeout(function () {
        hideCursor();
        setInterval(function () {
            hideCursor();
        }, 1000);
    }, 500);
    window.addEventListener("keyup", function (e) {
        showCursor();
        let value = document.getElementById("bash").value;
        if (e.key.length === 1) {
            document.getElementById("bash").value = value.substr(0, value.length - 1) + e.key + cursor;
        } else {
            switch (e.key) {
                case "Backspace":
                    if (!value.endsWith(linestart + cursor)) {
                        document.getElementById("bash").value = value.substr(0, value.length - 2) + cursor;
                    }
                    break;
                case "Enter":
                    document.getElementById("bash").value = value.substr(0, value.length - 1) + linestart + cursor;
                    break;
            }
        }
    });
});
