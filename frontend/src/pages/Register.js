import { useNavigate } from "react-router-dom";
import Formcomponent from "../components/Formcomponent";
import "../Custom.css";

export default function Register({ user, userChange, addUser }) {
    const navigate = useNavigate();
    const apiUrl = process.env.REACT_APP_API_URL;

    const defaultUser = {
        fname: '',
        lname: '',
        username: '',
        email: '',
        password: ''
    };
    user = user || defaultUser;

    const RegisterForm = 
        {inputs: [
            { type: 'text', name: 'fname', placeholder: 'First name', value: user.fname, changeFunc: userChange, icon: 'fa-feather' },
            { type: 'text', name: 'lname', placeholder: 'Last name', value: user.lname, changeFunc: userChange, icon: 'fa-feather'},
            { type: 'text', name: 'username', placeholder: 'User name', value: user.username, changeFunc: userChange, icon: 'fa-user' },
            { type: 'email', name: 'email', placeholder: 'Email', value: user.email, changeFunc: userChange, icon: 'fa-envelope' }, 
            { type: 'password', name: 'password', placeholder: 'Password', value: user.password, changeFunc: userChange, icon: 'fa-lock' }
        ],
        buttons: [{ type: 'submit', name: 'btn', label: 'Sign up' }]
    };

    const handleRegister = async (e) => {
        e.preventDefault();

        if (user.fname && user.lname && user.email && user.username && user.password) {
            try {
                const response = await fetch(`${apiUrl}/recipe/backend/users/signup.php`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        username: user.username,
                        password: user.password,
                        email: user.email,
                        first_name: user.fname,
                        last_name: user.lname,
                        profile: null,
                        role: "viewer"
                    })
                });
                const data = await response.json();
                if (data.id && data.hashed_password) {
                    localStorage.setItem("user_id", data.id);
                    localStorage.setItem("user_password", data.hashed_password);
                    addUser(user);
                    navigate("/mypage");
                } else {
                    alert("Error: " + (data.error || "Unknown error"));
                }
            } catch (error) {
                alert("Error: " + error.message);
            }
        } else {
            alert("Please fill out the form");
        }
    };

    return (
        <>  
            <div className="login">
                <h1 className="title">Register</h1>
                <Formcomponent elements={RegisterForm} onSubmit={handleRegister} />
            </div>
        </>
    );
}