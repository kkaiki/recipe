import { NavLink  } from "react-router-dom";
import "../Custom.css";
import logo from "../assets/images/logo.png";
import { useEffect, useState } from "react";

export default function Navbar({ isLoggedIn, onLogout }) {
  const [currentUser, setCurrentUser] = useState('');
  const [userRoll, setUserRoll] = useState('admin');
  const [roles, setRoles] = useState(['admin', 'viewer', 'editor']);
  const [selectedRole, setSelectedRole] = useState('');
  const [showPopup, setShowPopup] = useState(false);
  const [userList, setUserList] = useState([
    { id: 1, name: "Alice", role: "editor" },
    { id: 2, name: "Bob", role: "admin" },
    { id: 3, name: "Charlie", role: "viewer" },
  ]);
  
  const handleNavClick = () => {
      setShowPopup(true); // 팝업 열기
    };

  const handleClosePopup = () => {
    setShowPopup(false); // 팝업 닫기
  };



  const handleRoleChange = (e, userId) => {
  const updatedRole = e.target.value;
  setUserList((prevList) =>
    prevList.map((user) =>
      user.id === userId ? { ...user, role: updatedRole } : user
    )
  );
};
  useEffect(() => {
    const user_id = JSON.parse(localStorage.getItem('user_id'));
    const user_password = JSON.parse(localStorage.getItem('user_id'));
    
    if(user_id && user_password) {
      setCurrentUser(user_id);
    }
  }, [])
    return (
      <>
      <nav className="navbar navbar-expand-md navbar-light">
        <div className="container">
          <div className="navbar-header">
            <NavLink className="navbar-brand" to="/">
              <img src={logo} alt="logo" className="nav-logo" />
            </NavLink>
            <button
              className="navbar-toggler"
              type="button"
              data-bs-toggle="collapse"
              data-bs-target="#navbarNav"
            >
              <span className="navbar-toggler-icon"></span>
            </button>
          </div>
  
          <div className="collapse navbar-collapse" id="navbarNav">
            {isLoggedIn && (
              <div
                className="nav-link"
                style={{ whiteSpace: "nowrap", paddingTop: "20px" }}
              >
                <p style={{ color: "#C86322" }}>{currentUser}, logged in!</p>
              </div>
            )}
            <ul className="navbar-nav ms-auto align-items-center">
              <li className="nav-item">
                <NavLink
                  className="nav-link"
                  to="/"
                  activeClassName="active"
                >
                  Home
                </NavLink>
              </li>
              <li className="nav-item">
                <NavLink
                  className="nav-link"
                  to="/recipes"
                  activeClassName="active"
                >
                  Recipes
                </NavLink>
              </li>
              {isLoggedIn ? (
                <>
                  <li className="nav-item">
                    <NavLink
                      className="nav-link"
                      to="/mypage"
                      activeClassName="active"
                    >
                      MyPage
                    </NavLink>
                  </li>
                  <li className="nav-item">
                    <button className="btn btn-link nav-link" onClick={onLogout}>
                      Logout
                    </button>
                  </li>
                </>
              ) : (
                <>
                  <li className="nav-item">
                    <NavLink
                      className="nav-link"
                      to="/login"
                      activeClassName="active"
                    >
                      Login
                    </NavLink>
                  </li>
                  <li className="nav-item">
                    <NavLink
                      className="nav-link"
                      to="/register"
                      activeClassName="active"
                    >
                      Register
                    </NavLink>
                  </li>
                </>
              )}
              {
                (userRoll === 'admin' || userRoll === 'editor') && (
                  <li className="nav-item">
                    <NavLink
                      className="nav-link"
                      activeClassName="active"
                      to="/rolechange"
                      // onClick={handleNavClick}
                    >
                      Role Management
                    </NavLink>
                  </li>
                )}
            </ul>
          </div>
        </div>
      </nav>

      {showPopup && (
  <div className="popup-overlay">
    <div className="popup-content">
      <h3>Change User Role</h3>
      {/* 유저 리스트 테이블 */}
      <table className="user-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Role</th>
          </tr>
        </thead>
        <tbody>
          {userList.map((user) => (
            <tr key={user.id}>
              <td>{user.name}</td>
              <td>
                <select
                  value={user.role}
                  onChange={(e) => handleRoleChange(e, user.id)}
                >
                  {roles.map((role) => (
                    <option key={role} value={role}>
                      {role.charAt(0).toUpperCase() + role.slice(1)}
                    </option>
                  ))}
                </select>
              </td>
            </tr>
          ))}
        </tbody>
      </table>

      {/* 팝업 버튼 */}
      <div className="popup-buttons">
        <button onClick={handleClosePopup}>Close</button>
        <button
          onClick={() => {
            alert('Roles updated successfully!');
            handleClosePopup();
          }}
        >
          Save
        </button>
      </div>
    </div>
  </div>
)}
      </>
    );
}
