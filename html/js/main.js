(function ($) {
    "use strict";


    /*==================================================================
    [ Validate after type ]*/
    $('.validate-input .input100').each(function () {
        $(this).on('blur', function () {
            if (validate(this) == false) {
                showValidate(this);
            } else {
                $(this).parent().addClass('true-validate');
            }
        })
    })


    /*==================================================================
    [ Validate ]*/
    var input = $('.validate-input .input100');

    $('.validate-form').on('submit', function () {
        var check = true;

        for (var i = 0; i < input.length; i++) {
            if (validate(input[i]) == false) {
                showValidate(input[i]);
                check = false;
            }
        }

        return check;
    });


    $('.validate-form .input100').each(function () {
        $(this).focus(function () {
            hideValidate(this);
            $(this).parent().removeClass('true-validate');
        });
    });

    function validate(input) {
        if ($(input).attr('type') == 'email' || $(input).attr('name') == 'email') {
            if ($(input).val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/) == null) {
                return false;
            }
        } else {
            if ($(input).val().trim() == '') {
                return false;
            }
        }
    }

    function showValidate(input) {
        var thisAlert = $(input).parent();

        $(thisAlert).addClass('alert-validate');

        $(thisAlert).append('<span class="btn-hide-validate">&#xf136;</span>')
        $('.btn-hide-validate').each(function () {
            $(this).on('click', function () {
                hideValidate(this);
            });
        });
    }

    function hideValidate(input) {
        var thisAlert = $(input).parent();
        $(thisAlert).removeClass('alert-validate');
        $(thisAlert).find('.btn-hide-validate').remove();
    }



})(jQuery);

function sendMoney() {
    
    toggleLoader();
    
    var senderName = document.getElementById("senderName").value;
    var senderID = encrypt(document.getElementById("senderID").value).toString();
    var senderNumber = document.getElementById("senderNumber").value;
    var receiverNumber = document.getElementById("receiverNumber").value;
    var amount = parseInt(document.getElementById("amount").value);
    var reference = Math.floor(Math.random()*90000) + 10000;
    
    var uniqueID= Math.random().toString(36).substr(2, 9);
    
    
    var db = firebase.firestore();
    
    var vendorAmount = parseInt(localStorage.amount);
    var vendorNumber = localStorage.number;
    
    /*db.collection("transactions").doc(uniqueID).set({
        senderName: senderName,
        senderID: senderID,
        SenderNumber: senderNumber,
        receiverNumber: receiverNumber,
        amount: amount,
        retrieved: "false",
        reference: reference.toString(),
        timestamp: Date.now()
    })
        .then(function() {
            console.log("Document successfully written!");
            //send sms to person with ID
            
            var senderText = `Hi ${senderName}, \n You just sent ${amount} to ${receiverNumber}. \n Reference number:\n\n ${reference}. \n\n MAKE SURE YOUR FRIEND BRINGS AND ID TO COLLECT MONEY!`
            
            var receiverText = `Hi there, \n You have received ${amount} from ${senderName}. \n Reference: \n\n ${reference}\n\n Collect it from any Zuka station. Make sure to bring your ID`
            
            sendSMS(senderNumber, senderText);
            
            sendSMS(receiverNumber, receiverText);
            
            alert("Money sent: unique id is: "+reference);
        })
        .catch(function(error) {
            console.error("Error writing document: ", error);
        });*/
    
    if(vendorAmount < amount){
        alert('You do not have enough money to send');
        window.location.reload();
    }
    
    var receiver = JSON.parse(localStorage.motherloadString)[receiverNumber];
    
    if(receiver == undefined){
        //create receiver
        db.collection("users").doc(receiverNumber).set({
            id: null,
            name: null,
            surname: null,
            company: null,
            cellphone: receiverNumber,
            pass: null,
            amount: amount,
            timestamp: Date.now()
        })
            .then(function() {
                console.log("new user created");
                var newVendorAmount = vendorAmount - amount;
                updateSenderAmount(localStorage.number,newVendorAmount);
            
                //send smses
                var senderText = `Hi ${senderName}, \n You just sent ${amount} to ${receiverNumber}. \n Reference number:\n\n ${reference}. \n\n MAKE SURE YOUR FRIEND BRINGS AND ID TO COLLECT MONEY!`

                var receiverText = `Hi there, \n You have received ${amount} from ${senderName}. \n Reference: \n\n ${reference}\n\n Collect it from any Zuka station. Make sure to bring your ID`

                sendSMS(senderNumber, senderText);

                sendSMS(receiverNumber, receiverText);
            
                
            
                //TO DO: add to transaction load
            
                toggleLoader();
            
            
                //resetform
                resetForm(sendForm);
                
            
                
            })
            .catch(function(error) {
                console.error("Error writing document: ", error);
            });
        
    }
    else{
        var newVendorAmount = vendorAmount - amount;
        var newReceiverAmount = parseInt(receiver.amount) + amount;
        
        updateReceiverAmount(receiverNumber,newReceiverAmount);
        updateSenderAmount(vendorNumber,newVendorAmount);
        
        //send smses
        
        //send smses
        var senderText = `Hi ${senderName}, \n You sent ${amount} to ${receiverNumber}. \n Reference number:\n\n ${reference}. \n\n MAKE SURE YOUR FRIEND BRINGS AND ID TO COLLECT MONEY!`

        var receiverText = `Hi there, \n You have received ${amount} from ${senderName}. \n Reference: \n\n ${reference}\n\n Collect it from any Zuka station. Make sure to bring your ID`

        sendSMS(senderNumber, senderText);

        sendSMS(receiverNumber, receiverText);
        
        
        //add to log
        
        toggleLoader();
        resetForm("sendForm");
        
    }

}

function updateSenderAmount(number, newAmount){
    
    var db = firebase.firestore();
    
    var toBeUpdated = db.collection("users").doc(number);

    // Set the "capital" field of the city 'DC'
    return toBeUpdated.update({
        amount:parseInt(newAmount)
    })
    .then(function() {
        console.log("transaction complete!");
    })
    .catch(function(error) {
        // The document probably doesn't exist.
        console.error("Error updating document: ", error);
    });
    
}

function updateReceiverAmount(number, newAmount){
    
    console.log('called');
    
    var db = firebase.firestore();
    
    var toBeUpdated = db.collection("users").doc(number);

    // Set the "capital" field of the city 'DC'
    return toBeUpdated.update({
        amount:parseInt(newAmount)
    })
    .then(function() {
        console.log("transaction complete!");
    })
    .catch(function(error) {
        // The document probably doesn't exist.
        console.error("Error updating document: ", error);
    });
    
}

function sendSMS(number,message){
    
    $.post("https://cors-anywhere.herokuapp.com/https://rest.nexmo.com/sms/json?api_key=d6726b9a&api_secret=005e2f3453ccb56c&to=${number}&from=NEXMO&text=${message}", {
        },
        function (data, status) {
            console.log("Data: " + data + "\nStatus: " + status);
            alert(""+status);
        });
}

function getDetails(){
    
    var number = document.getElementById("referenceNumber").value;
    
    var transactions = getInfo("https://api.airtable.com/v0/appn8TIzmT0d92L4k/transactions?api_key=keynre40bTqHjQ7AD");
    
    transactions.records.map(transaction =>{
        console.log(transaction.id);
        
        var id = transaction.id;
        
        var reference = transaction.fields.Reference;
        
        console.log(id == number.trim(), id,number);
        
        if(reference == number.trim()){
            localStorage.transactionID = id;
            document.getElementById("receiveDetails").style.display = "block";
            document.getElementById("senderDetails").value = `${transaction.fields.SenderName} | ${decrypt(transaction.fields.SenderID)}` ;
            document.getElementById("receiverDetails").value = `${transaction.fields.ReceiverName} | ${decrypt(transaction.fields.ReceiverID)}`;
            document.getElementById("amount").value = transaction.fields.Amount;
        }
    })
    
    
    
    
}

function getInfo(url){
    
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.open("GET", url, false); // false for synchronous request
    xmlHttp.send(null);
    
    console.log(xmlHttp.responseText);
    
    return JSON.parse(xmlHttp.responseText);
}

function approve(){
    
    var id = localStorage.transactionID;
    
    var data = JSON.stringify({
        "records": [
            {
                "id": id,
                "fields": {
                    "Retrieved": "true"
                }
        }
      ]
    });

    var xhr = new XMLHttpRequest();

    xhr.addEventListener("readystatechange", function () {
        if (this.readyState === 4) {
            console.log(this.responseText);
        }
    });

    xhr.open("PATCH", "https://api.airtable.com/v0/appn8TIzmT0d92L4k/transactions?api_key=keynre40bTqHjQ7AD", false);
    xhr.setRequestHeader("Content-Type", "application/json");

    xhr.send(data);
}

function encrypt(message){
    
    var encrypted = CryptoJS.AES.encrypt(message, "SwaziPuppies");
    //U2FsdGVkX18ZUVvShFSES21qHsQEqZXMxQ9zgHy+bu0=
    
    return encrypted.toString()
    
}

function decrypt(message){
    var decrypted = CryptoJS.AES.decrypt(message, "SwaziPuppies");
    
    return decrypted.toString(CryptoJS.enc.Utf8)
}

function signup(){
    var id = encrypt(document.getElementById("signUpId").value);
    var name = document.getElementById("signUpName").value;
    var surname = document.getElementById("signUpSurname").value;
    var company = document.getElementById("signUpCompany").value;
    var cellphone = "+"+document.getElementById("signUpCellphone").value;
    var pass = encrypt(document.getElementById("signUpPass").value);
    var amount = 0;
    
    var db = firebase.firestore();
    
    db.collection("users").doc(cellphone).set({
        id: id,
        name: name,
        surname: surname,
        company: company,
        cellphone: cellphone,
        pass: pass,
        amount: 0,
        timestamp: Date.now()
    })
        .then(function() {
            console.log("Document successfully written!");
            alert('You have successfully been registered!');
            window.location.href="login.html"
        })
        .catch(function(error) {
            console.error("Error writing document: ", error);
        });
}

function login() {
    toggleLoader();
    
    var cellphone = "+"+document.getElementById("loginCellphone").value;
    var pass = document.getElementById("loginPass").value;
    
    var db = firebase.firestore();
    
    var users = db.collection("users").doc(cellphone);
    
    users.get().then(function(doc) {
        if (doc.exists) {
            
            if(pass == doc.data().pass){
                alert('Password error');
                window.location.href = "login.html";
            }
            
            
            localStorage.name = doc.data().name;
            localStorage.surname = doc.data().surname;
            localStorage.company = doc.data().company;
            localStorage.amount = doc.data().amount;
            localStorage.number = cellphone;
            console.log(`This user has ${localStorage.amount} Emalangeni`);
            toggleLoader();
            window.location.href = "send.html";
        } else {
            // doc.data() will be undefined in this case
            console.log("No such document!");
            
            alert('Looks like this user doesnt exist')
        }
    }).catch(function(error) {
        console.log("Error getting document:", error);
        alert(error)
    });
    
    
}

function setUpHome(){
    document.getElementById("title").innerHTML = `this user has ${localStorage.amount}`;
}

function toggleLoader(){
    
    var loader = document.getElementById("loader").style.display;
    
    if(loader == "block"){
        document.getElementById("loader").style.display = "none";
    }
    else{
        document.getElementById("loader").style.display = "block";
    }
}

function resetForm(id){
    document.getElementById(id).reset();
}