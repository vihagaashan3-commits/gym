function validateRegister(){
    let email = document.getElementById("email").value;
    let password = document.getElementById("password").value;

    if(password.length < 6){
        alert("Password must be at least 6 characters!");
        return false;
    }

    let pattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
    if(!email.match(pattern)){
        alert("Invalid Email Format!");
        return false;
    }

    return true;
}
