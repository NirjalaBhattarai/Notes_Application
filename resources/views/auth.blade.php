<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes Application - Authentication</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel= "stylesheet" href = "/css/auth.css">
</head>
<body>
    
    <div class="container">
        <div class="header">
            <h1>Notes App</h1>
            <p>Keep your thoughts organized</p>
        </div>
        
        <div class="form-container">
            <div class="alert" id="authAlert"></div>
            
            <!-- Login Form -->
            <div id="loginForm">

                <div class="form-group">
                    <label for="loginEmail">Email</label>
                    <input type="email" id="loginEmail" placeholder="Enter your email">
                </div>

                <div class="form-group">
                    <label for="loginPassword">Password</label>
                    <input type="password" id="loginPassword" placeholder="Enter your password">
                </div>

                <button class="btn" onclick="login()">Login</button>

                <div class="switch-form">
                    Don't have an account? <a onclick="showRegisterForm()">Register</a>
                </div>
            </div>
            
            <!-- Register Form -->
            <div id="registerForm" style="display: none;">

                <div class="form-group">
                    <label for="registerName">Name</label>
                    <input type="text" id="registerName" placeholder="Enter your name">
                </div>

                <div class="form-group">
                    <label for="registerEmail">Email</label>
                    <input type="email" id="registerEmail" placeholder="Enter your email">
                </div>

                <div class="form-group">
                    <label for="registerPassword">Password</label>
                    <input type="password" id="registerPassword" placeholder="Create a password">
                </div>

                <div class="form-group">

                    <label for="registerPasswordConfirmation">Confirm Password</label>
                    <input type="password" id="registerPasswordConfirmation" placeholder="Confirm your password">

                </div>
                <button class="btn" onclick="register()">Register</button>

                <div class="switch-form">
                    Already have an account? <a onclick="showLoginForm()">Login</a>
                </div>
            </div>
        </div>
    </div>

    <script>
   const API_BASE = '/api'; //  Laravel API url
        
        // Check if user is already logged in
        const token = localStorage.getItem('token');
        if (token) {
            // Redirect to notes page if already logged in
            window.location.href = '/notes';
        }
        
        function showAlert(message, isSuccess = true) {
            const alert = document.getElementById('authAlert');
            alert.textContent = message;
         alert.className = isSuccess ? 'alert alert-success' : 'alert alert-error';
         alert.style.display = 'block';
            
            setTimeout(() => {
                alert.style.display = 'none';
            }, 3000);
        }
        
        function showLoginForm() {

            document.getElementById('loginForm').style.display = 'block';
            document.getElementById('registerForm').style.display = 'none';
        }
        
        function showRegisterForm() {

            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('registerForm').style.display = 'block';
        }

        
        async function login() {
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            

            if (!email || !password) {
                showAlert('Please fill in all fields', false);
                return;
            }
            

            try {
                const response = await fetch(`${API_BASE}/login`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                  
                  
                    body: JSON.stringify({ email, password })

                });
                
                const data = await response.json();
                
                if (response.ok) {
                    // Save token and user data
                    localStorage.setItem('token', data.access_token);
                    localStorage.setItem('user', JSON.stringify(data.user));
                    
                    // Redirect to notes page
                    window.location.href = '/notes';
               
                } else {
                    showAlert(data.error || 'Login failed', false);
                }

            } catch (error) {
                showAlert('Network error. Please try again.', false);
            }
        }
        
        async function register() {
        
            const name = document.getElementById('registerName').value;
        
             const email = document.getElementById('registerEmail').value;
         
            const password = document.getElementById('registerPassword').value;
           
            const passwordConfirmation = document.getElementById('registerPasswordConfirmation').value;
            
            if (!name || !email || !password || !passwordConfirmation) {
                showAlert('Please fill in all fields', false);
                return;
            }

            
            if (password !== passwordConfirmation) {
                showAlert('Passwords do not match', false);
                return;
            }
            

            try {
                const response = await fetch(`${API_BASE}/register`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',   //body sending you is in json format
                        'Accept': 'application/json'         //res in json
                    },

                    body: JSON.stringify({ 
                        name, 
                        email, 
                        password, 
                password_confirmation: passwordConfirmation 
               

            })

                });
                

                const data = await response.json();
                
                if (response.ok) {
                    showAlert('Registration successful! Please login.', true);
                    showLoginForm();
                    // Clear registration form
                  document.getElementById('registerName').value = '';
                    document.getElementById('registerEmail').value = '';

              document.getElementById('registerPassword').value = '';

                  document.getElementById('registerPasswordConfirmation').value = '';
                
                
                } else {
                    const errorMsg = data.errors ? Object.values(data.errors).join(' ') : data.message;
                    showAlert(errorMsg || 'Registration failed', false);
                }


            } catch (error) {
                showAlert('Network error. Please try again.', false);
            }
        }
    </script>


</body>
</html>