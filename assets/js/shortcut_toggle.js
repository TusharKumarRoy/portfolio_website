document.addEventListener("keydown", function(event) {
    if (event.ctrlKey && event.altKey && event.key.toLowerCase() === ".") {
        const path = window.location.pathname;

        if (path.includes("/admin/dashboard.php")) {
            //window.location.href = "/index.php";
            window.location.href = "http://localhost/Full%20Stack%20Projects/portfolio_website/index.php";
        } else {
            //window.location.href = "/admin/dashboard.php";
             window.location.href = "http://localhost/Full%20Stack%20Projects/portfolio_website/admin/dashboard.php";
        }
    }
});
