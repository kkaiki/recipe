import { useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router-dom";
import defaultImage from "../assets/images/default.png";
import { useLocation } from "react-router-dom";
import axios from "axios";
import "../Custom.css";

export default function RecipePost() {
    const location = useLocation();
    const navigate = useNavigate();
    const [isAddStatus, setIsAddStatus] = useState(false);
    const [isEditStatus, setIsEditStatus] = useState(false);
    const [recipeUser, setRecipeUser] = useState('');
    const [date, setDate] = useState('');
    const { recipeId } = useParams();
    const [currentUser, setCurrentUser] = useState('');
    const [recipe, setRecipe] = useState({ 
        id: 0,
        title: '', 
        content: '', 
        category: '', 
        ingredients: [], // Initialize as an empty array
        image: defaultImage,
        likes: 0, 
        date: ''
    });
    const [ingredientInput, setIngredientInput] = useState(''); // Input for adding ingredients
    const [categories, setCategories] = useState([]); // State to store categories

    const handleAddIngredient = () => {
        if (!ingredientInput.trim()) {
            alert("Ingredient cannot be empty");
            return;
        }
        setRecipe((prev) => ({
            ...prev,
            ingredients: [...(prev.ingredients || []), ingredientInput]
        }));
        setIngredientInput(''); // Clear input after adding
    };

    const handleRemoveIngredient = (ingredientToRemove) => {
        setRecipe((prev) => ({
            ...prev,
            ingredients: prev.ingredients.filter((ingredient) => ingredient !== ingredientToRemove)
        }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        const userId = localStorage.getItem("user_id");
        const userPassword = localStorage.getItem("user_password");

        try {
            const recipeData = {
                local_storage_user_id: userId,
                local_storage_user_password: userPassword,
                name: recipe.title,
                description: recipe.content,
                is_active: 1,
                created_by: userId,
                created_at: recipe.date,
                image: recipe.image
            };

            const recipeResponse = await axios.post(`${process.env.REACT_APP_API_URL}/recipe/backend/recipe/post.php`, recipeData);
            const newRecipeId = recipeResponse.data.id;

            const ingredientPromises = recipe.ingredients.map(ingredient => {
                const ingredientData = {
                    name: ingredient,
                    recipe_id: newRecipeId,
                    local_storage_user_id: userId,
                    local_storage_user_password: userPassword
                };
                return axios.post(`${process.env.REACT_APP_API_URL}/recipe/backend/ingredient/post.php`, ingredientData);
            });

            await Promise.all(ingredientPromises);

            alert("Recipe and ingredients saved successfully");
            navigate('/recipes');
        } catch (error) {
            console.error("Error saving recipe and ingredients:", error);
            alert("Error saving recipe and ingredients");
        }
    };

    const handleImageChange = (e) => {
        const file = e.target.files[0];
        const reader = new FileReader();

        reader.onloadend = () => {
            setRecipe({ ...recipe, image: reader.result });
        };

        if(file) {
            reader.readAsDataURL(file);
        }
    }

    const handleCategoryClick = (category) => {
        setRecipe((prev) => {
            const currentCategories = prev.category ? prev.category.split(',').map(item => item.trim()) : [];

            if (currentCategories.includes(category)) {
                return {
                    ...prev,
                    category: currentCategories.filter(item => item !== category).join(', ')
                };
            } else {
                return {
                    ...prev,
                    category: currentCategories.length > 0 
                    ? [...currentCategories, category].join(', ') 
                    : category 
                };
            }
        });
    };

    useEffect(() => {
        const isEditPage = /^\/recipes\/edit\/\d+$/.test(location.pathname);
        if(isEditPage) {
            setIsEditStatus(true);
        } else {
            setIsEditStatus(false);
        }
    }, [])

    useEffect(() => {
        if (recipeId) {
            const storageData = JSON.parse(localStorage.getItem('recipe')) || [];
            const currentData = storageData.find(item => item.id === parseInt(recipeId));
            if (currentData) {
                setRecipe(currentData);
            }
        }
    }, [recipeId]);

    useEffect(() => {
        const user = JSON.parse(localStorage.getItem("user"));
        const recipeUser = JSON.parse(localStorage.getItem('recipe'));
        if(recipeUser && Array.isArray(recipeUser)) {
            const recipe = recipeUser[recipeId-1];
            if(recipe) {
                setRecipeUser(recipe.user);
            } else {
                setIsAddStatus(true);
            }
        } else {
            console.log('No recipes found in localStorage or invalid data structure');
        } 
        if (user) {
            setCurrentUser(user);
        
        } else {
            alert("Please Login first");
            navigate(-1);
        }
        
        const today = new Date().toISOString().split("T")[0];
        setDate(today); // date 状態更新
        setRecipe((prev) => ({ ...prev, date: today })); // recipe 객체에 날짜 추가
    }, [recipeId]);

    useEffect(() => {
        // Fetch categories from the backend
        axios.get(`${process.env.REACT_APP_API_URL}/recipe/backend/categories/get.php`)
            .then(response => {
                setCategories(response.data);
            })
            .catch(error => {
                console.error("Error fetching categories:", error);
            });
    }, []);

    return (
        <div className="container mt-5">
            <h1 className="text-center mb-4 title">{recipeId ? 'Recipe Detail' : 'Recipe Post'}</h1>
            <div className="col-12 edit-mode">
            </div>
            <form className="form-container" onSubmit={handleSubmit}>
                <div className="mb-3">
                    <label htmlFor="title" className="addform-label">Title</label>
                    <input 
                        type="text" 
                        className="addform-input" 
                        id="title"
                        onChange={(e) => setRecipe({...recipe, title: e.target.value })} 
                        value={recipe.title}
                        required 
                    />
                </div>
                {/* ingredients */}
                <div className="mb-3">
                    <label htmlFor="ingredients" className="addform-label">Ingredients</label>
                    <div className="input-group">
                        <input 
                            type="text" 
                            className="addform-input" 
                            placeholder="Add an ingredient"
                            value={ingredientInput}
                            onChange={(e) => setIngredientInput(e.target.value)}
                        />
                        <button type="button" className="tst-btn btn-success" onClick={handleAddIngredient}>
                            Add
                        </button>
                    </div>
                    <ul className="list-group">
                        {Array.isArray(recipe.ingredients) && recipe.ingredients.map((ingredient, index) => (
                            <li key={index} className="list-group-item d-flex justify-content-between align-items-center">
                                {ingredient}
                                <button
                                    type="button"
                                    className="btn btn-sm btn-danger"
                                    onClick={() => handleRemoveIngredient(ingredient)}
                                >
                                    Remove
                                </button>
                            </li>
                        ))}
                    </ul>
                </div> 
                <div className="mb-3">
                    <label htmlFor="content" className="addform-label">How to make</label>
                    <textarea 
                        className="addform-input" 
                        id="content" 
                        rows="3" 
                        onChange={(e) => setRecipe({...recipe, content: e.target.value })} 
                        value={recipe.content}  
                        required
                    ></textarea>
                </div>
                <div className="mb-3">
                    <label htmlFor="category" className="addform-label">Category</label>
                    <br />
                    <div className="btn-group-category" role="group" aria-label="Category Selector">
                        {categories.map((category) => (
                            <button
                                key={category.id}
                                type="button"
                                className={`btn ${recipe.category.split(',').map(item => item.trim()).includes(category.name) ? 'addform-category-btn' : 'addform-category-outline-btn'}`}
                                onClick={() => handleCategoryClick(category.name)}
                            >
                                {category.name.charAt(0).toUpperCase() + category.name.slice(1)}
                            </button>
                        ))}
                    </div>  
                </div>
                <div className="mb-3">
                    <label htmlFor="date" className="addform-label">Date</label>
                    <input type="date" className="addform-input" id="date" value={date} readOnly />
                </div>
             
                    <div className="mb-3">
                        <label htmlFor="img" className="addform-label">Image</label>
                           <input type="file" className="addform-input" id="img" accept="image/*" onChange={handleImageChange} />
                    </div>
             
                {recipe.image && (
                    <div className="mb-3">
                        <img src={recipe.image} alt="Preview" style={{ width: "300px", height: "300px" }} />
                    </div>
                )}
                {
                    isEditStatus ? (
                        <button type="submit" className="addform-btn">Update Recipe</button>
                    ) : (
                        <button type="submit" className="addform-btn">Create Recipe</button>
                    )
                }
            </form>
        </div>
    );
}