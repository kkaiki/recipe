import { useEffect, useState, useCallback } from "react";
import Formcomponent from "../components/Formcomponent";
import defaultProfile from "../assets/images/default-profile.png";
import defaultImage from "../assets/images/default.png";
import "../Custom.css";
import { Link, useNavigate } from "react-router-dom";
import axios from "axios";

export default function Mypage({ user, userChange, updateUser }) {
  const userId = localStorage.getItem("user_id");
  const userPassword = localStorage.getItem("user_password");

  const navigate = useNavigate();
  const [isEditing, setIsEditing] = useState(false);
  const [profileImage, setProfileImage] = useState(
    user?.profileImage || defaultProfile
  );
  const [likedRecipes, setLikedRecipes] = useState([]);
  const [uploadedRecipes, setUploadedRecipes] = useState([]);

  const fetchUserData = useCallback(() => {
    axios.post(`${process.env.REACT_APP_API_URL}/recipe/backend/users/get.php`, {
      local_storage_user_id: userId,
      local_storage_user_password: userPassword
    })
    .then(response => {
      const userData = response.data;
      const updatedUser = {
        username: userData.username,
        fname: userData.first_name,
        lname: userData.last_name,
        email: userData.email,
        password: userData.password,
        profileImage: userData.profile || defaultProfile
      };
      updateUser(updatedUser);
      setProfileImage(updatedUser.profileImage);
    })
    .catch(error => {
      console.error("Error fetching user data:", error);
      navigate("/login");
    });
  }, []);

  const fetchLikedRecipes = useCallback(() => {
    return new Promise((resolve) => {
      setTimeout(() => {
        axios.post(`${process.env.REACT_APP_API_URL}/recipe/backend/liked/get_liked_recipe.php`, {
          local_storage_user_id: userId,
          local_storage_user_password: userPassword
        })
        .then(response => {
          const uniqueLikedRecipes = Array.from(new Map(
            response.data.map(item => [item.id, item])
          ).values());
          setLikedRecipes(uniqueLikedRecipes);
          resolve();
        })
        .catch(error => {
          console.error("Error fetching liked recipes:", error);
          resolve();
        });
      });
    });
  }, [userId, userPassword]);

  const fetchUploadedRecipes = useCallback(() => {
    return axios.post(`${process.env.REACT_APP_API_URL}/recipe/backend/recipe/get_created_recipe.php`, {
      local_storage_user_id: userId,
      local_storage_user_password: userPassword
    })
    .then(response => {
      setUploadedRecipes(response.data);
    })
    .catch(error => {
      console.error("Error fetching uploaded recipes:", error);
      setUploadedRecipes([]);
    });
  }, [userId, userPassword]);

  useEffect(() => {
    if (!userId || !userPassword) {
      navigate("/login");
    } else {
      fetchUserData();
      fetchLikedRecipes();
      fetchUploadedRecipes();
      }
  }, [userId, userPassword, navigate, fetchUserData]);

  useEffect(() => {
    if (!user) return;

    const localStoragedData = JSON.parse(localStorage.getItem("recipe")) || [];

    const likedRecipesData = localStoragedData.filter((recipe) =>
      recipe.likedBy?.includes(user.email)
    );
    setLikedRecipes(likedRecipesData);

    // 사용자가 업로드한 레시피 필터링
    const uploadedRecipesData = localStoragedData.filter(
      (recipe) => recipe.user === user.email
    );
    setUploadedRecipes(uploadedRecipesData);

  }, [user]);

  useEffect(() => {
    setProfileImage(user?.profileImage || defaultProfile);
  }, [user]);

  const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onloadend = () => {
        const imageUrl = reader.result;
        setProfileImage(imageUrl);

        // users 배열 업데이트
        const users = JSON.parse(localStorage.getItem("users")) || [];
        const updatedUsers = users.map((u) => {
          if (u.email === user.email) {
            return { ...u, profileImage: imageUrl };
          }
          return u;
        });
        localStorage.setItem("users", JSON.stringify(updatedUsers));

        // 현재 user 업데이트
        const updatedUser = { ...user, profileImage: imageUrl };
        updateUser(updatedUser); // App.js에서 전달받은 함수로 user 상태 업데이트
        localStorage.setItem("user", JSON.stringify(updatedUser));
      };
      reader.readAsDataURL(file);
    }
  };

  const handleSubmit = (e) => {
    e.preventDefault();
  
    const updatedUser = {
      local_storage_user_id: userId,
      local_storage_user_password: userPassword,
      username: user.username,
      first_name: user.fname,
      last_name: user.lname,
      email: user.email,
      profile: profileImage
    };
  
    axios.put(`${process.env.REACT_APP_API_URL}/recipe/backend/users/put.php`, updatedUser)
      .then(response => {
        console.log(response.data.message);
        const users = JSON.parse(localStorage.getItem("users")) || [];
        const updatedUsers = users.map((u) => {
          if (u.email === user.email) {
            return { ...user };
          }
          return u;
        });
        localStorage.setItem("users", JSON.stringify(updatedUsers));
        localStorage.setItem("user", JSON.stringify(user));
        setIsEditing(false);
      })
      .catch(error => {
        console.error("Error updating user data:", error);
      });
  };

  const formElements = {
    inputs: [
      {
        type: "text",
        name: "username",
        placeholder: "Username",
        value: user.username,
        changeFunc: userChange,
        icon: "fa-user",
      },
      {
        type: "text",
        name: "fname",
        placeholder: "First Name",
        value: user.fname,
        changeFunc: userChange,
        icon: "fa-feather",
      },
      {
        type: "text",
        name: "lname",
        placeholder: "Last Name",
        value: user.lname,
        changeFunc: userChange,
        icon: "fa-feather",
      },
      {
        type: "password",
        name: "password",
        placeholder: "Password",
        value: user.password,
        changeFunc: userChange,
        icon: "fa-lock",
      },
    ],
    buttons: [
      {
        type: "submit",
        name: "save",
        label: "Save Changes",
      },
    ],
  };

  return (
    <div className="container mt-5">
      <h2 className="title">My Page</h2>

      <div className="profile-section">
        <div className="profile-image-container">
          <img src={profileImage} alt="Profile" className="profile-image" />
          <div className="image-upload">
            <label htmlFor="file-input" className="upload-button">
              <i className="fas fa-camera"></i>
            </label>
            <input
              id="file-input"
              type="file"
              accept="image/*"
              onChange={handleImageChange}
              style={{ display: "none" }}
            />
          </div>
        </div>

        <section className="my-4">
          <div className="d-flex justify-content-between align-items-center">
            <button
              className="edit-btn"
              onClick={() => setIsEditing(!isEditing)}
            >
              {isEditing ? "Cancel" : "Edit Profile"}
            </button>
          </div>

          {isEditing ? (
            <Formcomponent elements={formElements} onSubmit={handleSubmit} />
          ) : (
            <div className="profile-info">
              <div className="info-grid">
                <div className="info-item">
                  <label>Username</label>
                  <p>
                    <i className="fas fa-user"></i> {user.username}
                  </p>
                </div>
                <div className="info-item">
                  <label>First name</label>
                  <p>
                    <i className="fas fa-feather"></i> {user.fname}
                  </p>
                </div>
                <div className="info-item">
                  <label>Last name</label>
                  <p>
                    <i className="fas fa-feather"></i> {user.lname}
                  </p>
                </div>
                <div className="info-item">
                  <label>Email</label>
                  <p>
                    <i className="fas fa-envelope"></i> {user.email}
                  </p>
                </div>
              </div>
            </div>
          )}
        </section>

        <section className="recipes-section-green">
          <h4 className="section-title-green">Uploaded Recipes</h4>
          <div className="recipe-grid">
            {uploadedRecipes.length > 0 ? (
              uploadedRecipes.map((recipe) => (
                <Link
                  key={recipe.id}
                  to={`/recipes/${recipe.id}`}
                  className="recipe-card"
                >
                  <img
                    src={recipe.image 
                      ? (recipe.image.startsWith('data:image') 
                        ? recipe.image 
                        : `data:image/jpeg;base64,${recipe.image}`)
                      : defaultImage}
                    alt={recipe.name}
                    className="recipe-image"
                    onError={(e) => {
                      e.target.onerror = null;
                      e.target.src = defaultImage;
                    }}
                  />
                  <div className="recipe-content">
                    <h4 className="recipe-title">{recipe.name}</h4>
                    <p className="recipe-description">{recipe.description}</p>
                  </div>
                </Link>
              ))
            ) : (
              <div className="no-recipes">
                <i className="fas fa-utensils d-block"></i>
                No uploaded recipes yet.
              </div>
            )}
          </div>
        </section>

        <section className="recipes-section">
          <h4 className="section-title">Liked Recipes</h4>
          <div className="recipe-grid">
          {likedRecipes.map((recipe) => (
            <Link
              key={recipe.id}
              to={`/recipes/${recipe.id}`}
              className="recipe-card"
            >
              <img
                src={recipe.image 
                  ? (recipe.image.startsWith('data:image') 
                    ? recipe.image 
                    : `data:image/jpeg;base64,${recipe.image}`)
                  : defaultImage}
                alt={recipe.name}
                className="recipe-image"
                onError={(e) => {
                  e.target.onerror = null;
                  e.target.src = defaultImage;
                }}
              />
              <div className="recipe-content">
                <h4 className="recipe-title">{recipe.name}</h4>
              </div>
            </Link>
          ))}
          </div>
        </section>
      </div>
    </div>
  );
}
