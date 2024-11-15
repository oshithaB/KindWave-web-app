document.getElementById("showRegister").addEventListener("click", function() {
    document.getElementById("loginForm").style.display = "none";
    document.getElementById("registerForm").style.display = "block";
});

document.getElementById("showLogin").addEventListener("click", function() {
    document.getElementById("registerForm").style.display = "none";
    document.getElementById("loginForm").style.display = "block";
});

document.getElementById("userType").addEventListener("change", function() {
    var userType = this.value;
    if (userType === "donor") {
        document.getElementById("donorFields").style.display = "block";
        document.getElementById("recipientFields").style.display = "none";
    } else {
        document.getElementById("recipientFields").style.display = "block";
        document.getElementById("donorFields").style.display = "none";
    }
});
