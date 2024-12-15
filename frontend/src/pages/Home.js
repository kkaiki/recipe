import { Link } from 'react-router-dom';
import mainPic from "../assets/images/main.png"; 
import { useEffect, useState } from 'react';
import axios from 'axios';


export default function Home() {
    const [categories, setCategories] = useState([]); // 카테고리 상태
    const [recipes, setRecipes] = useState([]); // 레시피 상태

    // 카테고리와 레시피 데이터를 PHP API에서 가져오기
    useEffect(() => {
        axios
          .get("http://localhost/phptamwood/recipe/backend/categories/get.php")
          .then((response) => {
            if (Array.isArray(response.data)) {
              setCategories(response.data); // 카테고리를 상태로 저장
            } else {
              console.error("Response data is not an array.");
              setCategories([]); // 기본값 빈 배열
            }
          })
          .catch((error) => {
            console.error("Error fetching categories:", error);
          });
      }, []);

    // const [recipes, setRecipes] = useState([]);

    // useEffect(() => {
    //     const storagedRecipe = JSON.parse(localStorage.getItem('recipe'));
    //     if(storagedRecipe) {
    //         setRecipes(storagedRecipe);
    //     }
    // }, []);

    return (
        <div className="home-container">
            <header className="hero-header">
                <div className="hero-content">
                    <div className="hero-text">
                        <h2 className="hero-title">Cooking Made Fun and Easy: Unleash Your Inner Chef</h2>
                        <p className="hero-description">Discover recipes in your hand with the best recipe. Help you to find the easiest way to cook.</p>
                        <div className="btn-wrapper">
                            <Link to="/recipes" className="hero-btn">
                                Explore Recipes
                                <i className="fas fa-arrow-right"></i>
                            </Link>
                        </div>
                    </div>
                    <div className="hero-image">
                        <img src={mainPic} alt="main" />
                    </div>
                </div>
            </header>

            <section className="categories-section">
                <h2 className="categories-title">Popular Categories</h2>
                <div className="categories-grid">
                    {categories.map((category) => (
                        <Link 
                            to={`/recipes?category_id=${category.id}`} // id를 기반으로 경로 생성
                            key={category.id} 
                            className="category-card"
                        >
                            <div className="category-image-container">
                                <img 
                                    src={category.image} 
                                    alt={category.name} 
                                    className="category-image" 
                                />
                            </div>
                            <h3 className="category-name">{category.name}</h3>
                        </Link>
                    ))}
                </div>
            </section>
            <section>
                <h2 className="categories-title">Latest Recipe</h2>
                <div className='container py-5'>
                    <div className='row g-4'>
                        {recipes.length > 0 ?
                            recipes.map((recipe, idx) => (
                            <div className='col-md-4' key={recipe.id}>
                                <div className='recipe-card'>
                                    <Link to={`/recipes/${recipe.id}`} className="card h-100 text-decoration-none">
                                        <img 
                                            src={recipe.image} 
                                            alt={recipe.title} 
                                            className="recipe-image" 
                                        />
                                        <div className='recipe-content'>
                                            <h3 className="card-title">{recipe.title}</h3>
                                            <p className="card-text">
                                                <i className="fas fa-user me-2"></i>
                                                Recipe by {recipe.user}
                                            </p>
                                            <p className="card-text">
                                                <i className="fas fa-tag me-2"></i>
                                                <strong>Category:</strong> {recipe.category}
                                            </p>
                                        </div>
                                    </Link>
                                </div>
                            </div>
                        )) :    
                            <div className="no-data">
                                <i className="fas fa-search mb-3 d-block" style={{fontSize: "2rem"}}></i>
                                There is no Recipe
                            </div> 
                        }
                    </div>
                </div>
            </section>
        </div>
    );
}