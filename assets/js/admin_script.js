document.addEventListener("keydown", function(event) {
 
    if (event.ctrlKey && event.altKey && event.key.toLowerCase() === ".") {
        window.location.href = "http://localhost/Full%20Stack%20Projects/portfolio_website/index.php";
    }
});