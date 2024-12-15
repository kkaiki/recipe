import React, { Component } from "react";
import Formcomponent from "../components/Formcomponent";
import { withRouter } from "../utils/withRouterReplacement"; // 커스텀 withRouter 사용

class Login extends Component {
    constructor(props) {
        super(props);
        
        this.handleLoginSubmit = this.handleLoginSubmit.bind(this);
    }

    componentDidMount() {
        const userId = localStorage.getItem("user_id");
        const userPassword = localStorage.getItem("user_password");

        if (userId && userPassword) {
            this.props.navigate("/mypage");
        }
    }

    async handleLoginSubmit(e) {
        e.preventDefault();

        const { user, handleLogin, navigate } = this.props; // props에서 user, handleLogin, navigate 추출

        if (!user) {
            alert("User information is missing.");
            return;
        }

        try {
            const response = await fetch(`${process.env.REACT_APP_API_URL}/recipe/backend/users/login.php`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    email: user.email,
                    password: user.password
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.id && data.password) {
                localStorage.setItem("user_id", data.id);
                localStorage.setItem("user_password", data.password);
                handleLogin(data);
                navigate("/mypage");
            } else {
                alert("Invalid email or password.");
            }
        } catch (error) {
            alert("Error: " + error.message);
        }
    }

    render() {
        const { user, userChange } = this.props;

        // user オブジェクトが null または undefined の場合にデフォルト値を設定
        const defaultUser = {
            email: '',
            password: ''
        };
        const currentUser = user || defaultUser;

        const LoginForm = {
            inputs: [
                { type: 'email', name: 'email', placeholder: 'Email address', value: currentUser.email, changeFunc: userChange, icon: 'fa-envelope' },
                { type: 'password', name: 'password', placeholder: 'Password', value: currentUser.password, changeFunc: userChange, icon: 'fa-lock'}
            ],
            buttons: [{ type: 'submit', name: 'btn', label: 'login' }]
        };

        return (
            <div className="login">
                <h1 className="title">Login</h1>
                <Formcomponent elements={LoginForm} onSubmit={this.handleLoginSubmit} />
            </div>
        );
    }
}

export default withRouter(Login);