import React, { useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router-dom";
import axios from "axios";

export default function RecipeDetail() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [recipe, setRecipe] = useState(null);
  const [loggedInUser, setLoggedInUser] = useState("");
  const [uploadedUser, setUploadUser] = useState("");
  const [comments, setComments] = useState([]);
  const [newComment, setNewComment] = useState("");
  const [editingCommentIndex, setEditingCommentIndex] = useState(null);
  const [editedComment, setEditedComment] = useState("");
  const [likes, setLikes] = useState(0);

  const onClickEditRecipe = (recipeId) => {
    navigate(`/recipes/edit/${recipeId}`);
  };

  useEffect(() => {
    axios
      .get(`http://localhost/recipe/backend/recipe/get.php?id=${id}`)
      .then((response) => {
        const foundRecipe = response.data;
        if (foundRecipe) {
          setRecipe(foundRecipe);
        } else {
          alert("Recipe not found!");
        }
      })
      .catch((error) => {
        console.error("Error fetching recipe details:", error);
        alert("Error fetching recipe details!");
      });
  }, [id]);

  useEffect(() => {
    const currentUser = JSON.parse(localStorage.getItem("user"));
    if (currentUser) {
      setLoggedInUser(currentUser.email);
    }
  }, []);

  useEffect(() => {
    axios
      .get(`http://localhost/recipe/backend/liked/get.php?recipe_id=${id}`)
      .then((response) => {
        const likeData = response.data;
        setLikes(likeData.length);
      })
      .catch((error) => {
        console.error("Error fetching likes:", error);
      });
  }, [id]);

  const fetchComments = () => {
    axios
      .get(`http://localhost/recipe/backend/comment/get.php?recipe_id=${id}`)
      .then((response) => {
        const commentData = response.data.data.map(comment => ({
          user: comment.created_by,
          text: comment.comment,
          date: comment.created_at
        }));
        setComments(commentData);
      })
      .catch((error) => {
        console.error("Error fetching comments:", error);
      });
  };

  useEffect(() => {
    fetchComments();
  }, [id]);

  const handleAddComment = () => {
    if (!newComment.trim()) {
      alert("Comment cannot be empty!");
      return;
    }

    const userId = localStorage.getItem("user_id");
    const userPassword = localStorage.getItem("user_password");

    axios
      .post(`http://localhost/recipe/backend/comment/post.php`, {
        comment: newComment,
        recipe_id: id,
        created_by: userId,
        local_storage_user_id: userId,
        local_storage_user_password: userPassword
      })
      .then((response) => {
        setNewComment("");
        fetchComments();
      })
      .catch((error) => {
        console.error("Error adding comment:", error);
        alert("Error adding comment!");
      });
  };

  const handleEditComment = (index) => {
    setEditingCommentIndex(index);
    setEditedComment(comments[index].text);
  };

  const handleSaveEditedComment = () => {
    const updatedComments = comments.map((comment, index) => {
      if (index === editingCommentIndex) {
        return { ...comment, text: editedComment };
      }
      return comment;
    });

    setComments(updatedComments);
    setEditingCommentIndex(null);
    setEditedComment("");

    // Update comments in localStorage
    const storedRecipes = JSON.parse(localStorage.getItem("recipe")) || [];
    const updatedRecipes = storedRecipes.map((item) => {
      if (item.id === parseInt(id)) {
        return { ...item, comments: updatedComments };
      }
      return item;
    });
    localStorage.setItem("recipe", JSON.stringify(updatedRecipes));
  };

  const handleDeleteComment = (index) => {
    const updatedComments = comments.filter((_, i) => i !== index);
    setComments(updatedComments);

    // Update comments in localStorage
    const storedRecipes = JSON.parse(localStorage.getItem("recipe")) || [];
    const updatedRecipes = storedRecipes.map((item) => {
      if (item.id === parseInt(id)) {
        return { ...item, comments: updatedComments };
      }
      return item;
    });
    localStorage.setItem("recipe", JSON.stringify(updatedRecipes));
  };

  if (!recipe) {
    return <div className="container mt-5">Loading recipe details...</div>;
  }

  return (
    <div className="container mt-5">
      <div className="recipe-detail">
        <div className="text-center mb-4">
          <img
            src={recipe.image}
            alt={recipe.name}
            className="img-fluid rounded"
            style={{ maxHeight: "400px", objectFit: "cover" }}
          />
        </div>

        <div className="recipe-meta text-center mb-5">
          <h1 className="recipe-name">{recipe.name}</h1>
          <p className="text-muted">Recipe by: {recipe.username}</p>
          <p className="text-muted">Date: {recipe.created_at}</p>

          {loggedInUser === uploadedUser && (
            <button
              type="submit"
              className="edit-btn btn-primary"
              onClick={() => onClickEditRecipe(id)}
            >
              Edit
            </button>
          )}
        </div>

        <div className="row text-center mb-4">
          <div className="col-md-6">
            <h6 className="side-title">Category</h6>
            <p className="badge bg-primary">{recipe.categories}</p>
          </div>
          <div className="col-md-6">
            <h6 className="side-title">Likes</h6>
            <p className="badge bg-danger">{likes}</p>
          </div>
        </div>

        {/* Ingredients Section */}
        <div className="recipe-ingredients">
          <h3 className="ingredients-title">Ingredients</h3>
          {recipe.ingredients && recipe.ingredients.length > 0 ? (
            <ul className="list-group mb-4">
              {recipe.ingredients.split(',').map((ingredient, index) => (
                <li key={index} className="list-group-item">
                  {ingredient}
                </li>
              ))}
            </ul>
          ) : (
            <p className="info-p text-center">No ingredients provided for this recipe.</p>
          )}
        </div>

        {/* How to Make Section */}
        <div className="recipe-content-in">
          <h3 className="make-title">How to make?</h3>
          <p style={{ whiteSpace: "pre-wrap" }}>{recipe.description}</p>
        </div>

        {/* Comments Section */}
        <div className="comments-section mt-5">
          <h3 className="comment-title">Comments</h3>
          <div className="comment-list">
            {comments.length > 0 ? (
              comments.map((comment, index) => (
                <div key={index} className="comment mb-3">
                  <p>
                    <strong>{comment.user}</strong> <span className="text-muted">({comment.date})</span>
                  </p>
                  {editingCommentIndex === index ? (
                    <div>
                      <textarea
                        className="form-control mb-2"
                        value={editedComment}
                        onChange={(e) => setEditedComment(e.target.value)}
                      ></textarea>
                      <button
                        className="edit-co-btn btn-success me-2"
                        onClick={handleSaveEditedComment}
                      >
                        Save
                      </button>
                      <button
                        className="delete-co-btn btn-secondary"
                        onClick={() => setEditingCommentIndex(null)}
                      >
                        Cancel
                      </button>
                    </div>
                  ) : (
                    <p>{comment.text}</p>
                  )}
                  {comment.user === loggedInUser && editingCommentIndex !== index && (
                    <div>
                      <button
                        className="edit-co-btn btn-sm btn-warning me-2"
                        onClick={() => handleEditComment(index)}
                      >
                        Edit
                      </button>
                      <button
                        className="delete-co-btn btn-sm btn-danger"
                        onClick={() => handleDeleteComment(index)}
                      >
                        Delete
                      </button>
                    </div>
                  )}
                </div>
              ))
            ) : (
              <p className="info-p text-center">No comments yet. Be the first to comment!</p>
            )}
          </div>
          <div className="comment-form mt-4">
            <textarea
              className="form-control mb-2"
              placeholder="Write your comment here..."
              value={newComment}
              onChange={(e) => setNewComment(e.target.value)}
            ></textarea>
            <button className="add-btn btn-primary" onClick={handleAddComment}>
              Add Comment
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}