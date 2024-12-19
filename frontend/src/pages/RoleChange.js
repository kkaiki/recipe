import React, { useState } from "react";

const RoleChange = () => {
  const [userList, setUserList] = useState([
    { id: 1, name: "Alice", role: "editor" },
    { id: 2, name: "Bob", role: "admin" },
    { id: 3, name: "Charlie", role: "viewer" },
  ]);

  const roles = ["viewer", "editor", "admin"]; // 역할 목록

  // 역할 변경 핸들러
  const handleRoleChange = (e, userId) => {
    const updatedRole = e.target.value;

    setUserList((prevList) =>
      prevList.map((user) =>
        user.id === userId ? { ...user, role: updatedRole } : user
      )
    );
  };

  return (
    <div className="container">
      <h1 className="my-4">Change User Roles</h1>
      <table className="table table-striped">
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
    </div>
  );
};

export default RoleChange;
