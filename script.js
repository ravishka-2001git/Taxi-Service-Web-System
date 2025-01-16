var data= {
    chatinit:{
        title: ["Hello <span class='emoji'> &#128075;</span>","I am Mr. Ravi","How can I help you?"],
        options: ["Booking","Login","Addition Service","Driver","Contact","About Us"]

    },

    booking: {
        title:["Are you registered to this City Taxi Service web page..? 🤔"],
        options:["Yes registered", "No I'm not registered"],
        url : {
            
        }
    },

    yes: {
        title:["Thank you for choosing our Taxi service..❤️", "Click the button below to book your ride.🚕"],
        options:['Book Now'],
        url : {
            more:"Booking.html",
            link:["Booking.html"]
        }
    },

    no: {
        title:["Please click the button below and register on our website.🫡"],
        options:['Registation'],
        url : {
            more:"Login.html",
            link:["Login.html"]
        }
    },


    addition: {
        title:["Explore additional services we offer to enhance your travel experience.", "Please click the button below..😗"],
        options:['Our Service'],
        url : {
            more:"Services.html",
            link:["Services.html"]
        }
    },

    driver: {
        title:["Are you registered to this City Taxi Service web page..? 🤔"],
        options:["Registered", "Not registered"],
        url : {
            
        }
    },

    registered: {
        title:["Log in to your driver account to access your dashboard and manage your bookings.", "Please click the button below..😁"],
        options:['Drivers Login'],
        url : {
            more:"Login_Drivers.html",
            link:["Login_Drivers.html"]
        }
    },

    not: {
        title:["Please click the button below and register on our website.🫡"],
        options:['Driver Registation'],
        url : {
            more:"Drivers.html",
            link:["Drivers.html"]
        }
    },


    contact: {
        title:["Get in touch with us for any inquiries or support..📞🙂‍↔️"],
        options:['Contact'],
        url : {
            more:"Contact.html",
            link:["Contact.html"]
        }
    },

    login: {
        title:["Are you registered to this City Taxi Service web page..? 🤔"],
        options:["Yeahh", "Nopp"],
        url : {
            
        }
    },

    yeahh: {
        title:["Thank you for choosing our Taxi service..❤️", "Log in to your account to manage bookings and access exclusive features."],
        options:['Login'],
        url : {
            more:"Login.html",
            link:["Login.html"]
        }
    },

    nopp: {
        title:["Please click the button below and register on our website.🫡"],
        options:['Registation'],
        url : {
            more:"Login.html",
            link:["Login.html"]
        }
    },

    about: {
        title:["Thanks for your response","Learn more about our mission, values, and how we serve the community.🤗❤️","Click on it to know more"],
        options:['About us'],
        url : {
            more:"About.html",
            link:["About.html"]
        }
    },
}

document.getElementById("init").addEventListener("click",showChatBot);
var cbot= document.getElementById("chat-box");

var len1= data.chatinit.title.length;

function showChatBot(){
    console.log(this.innerText);
    if(this.innerText=='START CHAT'){
        document.getElementById('test').style.display='block';
        document.getElementById('init').innerText='CLOSE CHAT';
        initChat();
    }
    else{
        location.reload();
    }
}

function initChat(){
    j=0;
    cbot.innerHTML='';
    for(var i=0;i<len1;i++){
        setTimeout(handleChat,(i*500));
    }
    setTimeout(function(){
        showOptions(data.chatinit.options)
    },((len1+1)*500))
}

var j=0;
function handleChat(){
    console.log(j);
    var elm= document.createElement("p");
    elm.innerHTML= data.chatinit.title[j];
    elm.setAttribute("class","msg");
    cbot.appendChild(elm);
    j++;
    handleScroll();
}

function showOptions(options){
    for(var i=0;i<options.length;i++){
        var opt= document.createElement("span");
        var inp= '<div>'+options[i]+'</div>';
        opt.innerHTML=inp;
        opt.setAttribute("class","opt");
        opt.addEventListener("click", handleOpt);
        cbot.appendChild(opt);
        handleScroll();
    }
}

function handleOpt(){
    console.log(this);
    var str= this.innerText;
    var textArr= str.split(" ");
    var findText= textArr[0];
    
    document.querySelectorAll(".opt").forEach(el=>{
        el.remove();
    })
    var elm= document.createElement("p");
    elm.setAttribute("class","test");
    var sp= '<span class="rep">'+this.innerText+'</span>';
    elm.innerHTML= sp;
    cbot.appendChild(elm);

    console.log(findText.toLowerCase());
    var tempObj= data[findText.toLowerCase()];
    handleResults(tempObj.title,tempObj.options,tempObj.url);
}

function handleDelay(title){
    var elm= document.createElement("p");
        elm.innerHTML= title;
        elm.setAttribute("class","msg");
        cbot.appendChild(elm);
}


function handleResults(title,options,url){
    for(let i=0;i<title.length;i++){
        setTimeout(function(){
            handleDelay(title[i]);
        },i*500)
        
    }

    const isObjectEmpty= (url)=>{
        return JSON.stringify(url)=== "{}";
    }

    if(isObjectEmpty(url)==true){
        console.log("having more options");
        setTimeout(function(){
            showOptions(options);
        },title.length*500)
        
    }
    else{
        console.log("end result");
        setTimeout(function(){
            handleOptions(options,url);
        },title.length*500)
        
    }
}

function handleOptions(options,url){
    for(var i=0;i<options.length;i++){
        var opt= document.createElement("span");
        var inp= '<a class="m-link" href="'+url.link[i]+'">'+options[i]+'</a>';
        opt.innerHTML=inp;
        opt.setAttribute("class","opt");
        cbot.appendChild(opt);
    }
    var opt= document.createElement("span");
    var inp= '<a class="m-link" href="'+url.more+'">'+'See more</a>';

    const isObjectEmpty= (url)=>{
        return JSON.stringify(url)=== "{}";
    }

    console.log(isObjectEmpty(url));
    console.log(url);
    opt.innerHTML=inp;
    opt.setAttribute("class","opt link");
    cbot.appendChild(opt);
    handleScroll();
}

function handleScroll(){
    var elem= document.getElementById('chat-box');
    elem.scrollTop= elem.scrollHeight;
}

function sendMessage(event) {
    if (event.key === 'Enter') {
        const input = document.getElementById('chat-input');
        const message = input.value;
        if (message.trim() !== '') {
            displayMessage('User', message);
            input.value = '';
            fetch('/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ message: message })
            })
            .then(response => response.json())
            .then(data => {
                displayMessage('Bot', data.response);
            });
        }
    }
}

function sendButtonMessage(message) {
    displayMessage('User', message);
    fetch('/chat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ message: message })
    })
    .then(response => response.json())
    .then(data => {
        displayMessage('Bot', data.response);
    });
}

function displayMessage(sender, message) {
    const chatBox = document.getElementById('chat-box');
    const messageElement = document.createElement('div');
    messageElement.className = `message ${sender.toLowerCase()}`;
    messageElement.textContent = message;
    chatBox.appendChild(messageElement);
    chatBox.scrollTop = chatBox.scrollHeight;
}

// JavaScript for Booking Form

document.getElementById('bookingForm').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent form from submitting the traditional way
    
    // Get form values
    const name = document.getElementById('name').value;
    const phone = document.getElementById('phone').value;
    const pickup = document.getElementById('pickup').value;
    const dropoff = document.getElementById('dropoff').value;
    const time = document.getElementById('time').value;
    
    // Basic validation
    if (name && phone && pickup && dropoff && time) {
        document.getElementById('bookingMessage').innerText = 'Booking confirmed! You will receive a call shortly.';
        document.getElementById('bookingMessage').style.color = 'green';

        // Clear form after successful submission
        document.getElementById('bookingForm').reset();
    } else {
        document.getElementById('bookingMessage').innerText = 'Please fill out all fields correctly.';
        document.getElementById('bookingMessage').style.color = 'red';
    }
});



// Get the booking and driver dropdown elements
const bookingBtn = document.querySelector('.nav-link'); // First nav-link for Booking
const driverBtn = document.querySelectorAll('.nav-link')[1]; // Second nav-link for Driver

const bookingDropdownMenu = document.querySelector('.dropdown-menu');
const driverDropdownMenu = document.querySelectorAll('.dropdown-menu')[1];

// Add click event listener for Booking
bookingBtn.addEventListener('click', function(event) {
    event.preventDefault();
    toggleDropdown(bookingDropdownMenu);
});

// Add click event listener for Driver
driverBtn.addEventListener('click', function(event) {
    event.preventDefault();
    toggleDropdown(driverDropdownMenu);
});

// Function to toggle the dropdown visibility
function toggleDropdown(menu) {
    if (menu.style.display === "block") {
        menu.style.display = "none";
    } else {
        menu.style.display = "block";
    }
}

// Close the dropdown if clicking outside
window.addEventListener('click', function(event) {
    if (!event.target.matches('.nav-link')) {
        bookingDropdownMenu.style.display = "none";
        driverDropdownMenu.style.display = "none";
    }
});


function sendMail(){
    let parms ={
        fullName : document.getElementById("fullName").value,
        Username : document.getElementById("Username").value,
        email : document.getElementById("email").value,
        phone : document.getElementById("phone").value,
        password : document.getElementById("password").value,
    }

    emailjs.send("service_gq4mt8d","template_lt15xd2",parms).then(alert("Email Sent!!"))



}


function toggleStatus() {
    const statusElement = document.getElementById("driverStatus");
    if (statusElement.innerText === "AVAILABLE") {
        statusElement.innerText = "BUSY";
    } else {
        statusElement.innerText = "AVAILABLE";
    }
}


// JavaScript functions to handle approval and decline actions

function approveDriver(button) {
    if (confirm("Are you sure you want to approve this booking?")) {
        let row = button.parentNode.parentNode;
        row = button.remove();
        alert("Driver approved successfully.");
    }
}

function declineDriver(button) {
    if (confirm("Are you sure you want to delete this booking?")) {
        let row = button.parentNode.parentNode;
        row.remove();
        alert("Driver removed successfully.");
    }
}




// Rating

const allStar = document.querySelectorAll('.rating .star')
const ratingValue = document.querySelector('.rating input')

allStar.forEach((item, idx)=> {
	item.addEventListener('click', function () {
		let click = 0
		ratingValue.value = idx + 1

		allStar.forEach(i=> {
			i.classList.replace('bxs-star', 'bx-star')
			i.classList.remove('active')
		})
		for(let i=0; i<allStar.length; i++) {
			if(i <= idx) {
				allStar[i].classList.replace('bx-star', 'bxs-star')
				allStar[i].classList.add('active')
			} else {
				allStar[i].style.setProperty('--i', click)
				click++
			}
		}
	})
})