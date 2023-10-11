let userBox = document.querySelector('.header .header-2 .user-box');

document.querySelector('#user-btn').onclick = () =>{
   userBox.classList.toggle('active');
   navbar.classList.remove('active');
}

let navbar = document.querySelector('.header .header-2 .navbar');

document.querySelector('#menu-btn').onclick = () =>{
   navbar.classList.toggle('active');
   userBox.classList.remove('active');
}

window.onscroll = () =>{
   userBox.classList.remove('active');
   navbar.classList.remove('active');

   if(window.scrollY > 60){
      document.querySelector('.header .header-2').classList.add('active');
   }else{
      document.querySelector('.header .header-2').classList.remove('active');
   }
}
document.addEventListener("DOMContentLoaded", function () {
    const generateBillButton = document.getElementById("generateBillButton");

    generateBillButton.addEventListener("click", function () {
        // Check if the bill has already been generated
        const billGenerated = sessionStorage.getItem("billGenerated");
        
        if (billGenerated) {
            alert("Bill has already been generated.");
            return;
        }

        // Here, you can use AJAX to send a request to your server to generate the bill
        // and clear the user's cart.

        // Assuming you have a server-side script (e.g., generate_bill.php) to generate the bill,
        // you can use the fetch API to send a request.

        fetch("generate_bill.php", {
            method: "POST",
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Display a success message with the bill ID
                alert("Bill generated successfully! Bill ID: " + data.billId);

                // Clear the cart by redirecting the user to a cart-clearing page
                sessionStorage.setItem("billGenerated", "true"); // Mark bill as generated
                window.location.href = "clear_cart.php";
            } else {
                // Display an error message if bill generation fails
                alert("Error generating the bill. Please try again later.");
            }
        })
        .catch(error => {
            console.error("Error:", error);
        });
    });
});
