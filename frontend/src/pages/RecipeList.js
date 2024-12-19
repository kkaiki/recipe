import { useNavigate, useLocation } from "react-router-dom";
import AllRecipes from "../components/AllRecipes";
import { useEffect, useState } from "react";
import axios from "axios";

export default function RecipeList() {
  const location = useLocation();
  const navigate = useNavigate();
  //const [recipes, setRecipes] = useState([]);
  const [categories, setCategories] = useState([]); // 카테고리 상태 추가
  const [searchWord, setSearchWord] = useState("");

  const queryParams = new URLSearchParams(location.search);
  const initialCategory = queryParams.get("category");
  const [category, setCategory] = useState(
    initialCategory?.charAt(0).toUpperCase() + initialCategory?.slice(1) ||
      "All"
  );

  // 카테고리 데이터 가져오기
  useEffect(() => {
    axios
      .get(`${process.env.REACT_APP_API_URL}/recipe/backend/categories/get.php`)
      .then((response) => {
        console.log(response.data);
        if (Array.isArray(response.data)) {
          setCategories(response.data); // PHP에서 가져온 카테고리 데이터를 저장
          console.log(categories); //
        } else {
          console.error("Response data is not an array.");
          setCategories([]); // 빈 배열로 초기화
        }
      })

      .catch((error) => {
        console.error("Error fetching categories:", error);
      });
  }, []);

  useEffect(() => {
    console.log("Updated Categories:", categories);
  }, [categories]);

  // 카테고리 변경 핸들러
  const setCategoryHandler = (selectedCategory) => {
    setCategory(selectedCategory);
    navigate(`/recipes?category=${selectedCategory.toLowerCase()}`);
  };

  // useEffect(() => {
  //   const localStoragedData = JSON.parse(localStorage.getItem("recipe"));

  //   if (localStoragedData) {
  //     const processedData = Array.isArray(localStoragedData)
  //       ? localStoragedData
  //       : [localStoragedData];

  //     const dataWithId = processedData.map((recipe, index) => ({
  //       ...recipe,
  //       id: recipe.id || index + 1,
  //       liked: recipe.liked || false,
  //     }));

  //     setRecipes(dataWithId);
  //   } else {
  //     setRecipes([]);
  //   }
  // }, []);

  const handleClick = () => {
    navigate("/recipes/new");
  };

  // const handleCategory = (selectedCategory) => {
  //   setCategory(selectedCategory);
  // };

  const handleSearch = (e) => {
    const word = e.target.value;
    setSearchWord(word);
  };

  return (
    <div className="container-fluid">
      <div className="row">
        <h1 className="title">Recipes</h1>
        <aside className="col-md-3 d-none d-md-block">
          <div className="categories-sidebar">
            {/* <h2 className="categories-title">Categories</h2> */}
            <div className="category-buttons">
              {categories.map((cate) => (
                <button
                  key={cate.id} // 고유한 ID 사용
                  className={`category-btn ${
                    category === cate.name ? "active" : ""
                  }`}
                  onClick={() => setCategoryHandler(cate.name)} // cate.name을 사용
                >
                  {cate.name}
                </button>
              ))}
            </div>
          </div>
        </aside>

        <main className="col-md-9">
          <div className="search-section">
            <div className="search-container">
              <input
                type="text"
                className="search-input"
                placeholder="Search recipes..."
                value={searchWord}
                onChange={handleSearch}
              />
              <button
                type="button"
                className="add-recipe-btn"
                onClick={handleClick}
              >
                <i className="fas fa-plus"></i>
                Add Recipe
              </button>
            </div>
          </div>

          {/* <AllRecipes 
          category={category} 
          PropsRecipes={recipes} 
          searchWord={searchWord} 
        /> */}
        </main>
      </div>
    </div>
  );
}
