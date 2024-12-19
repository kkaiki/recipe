## Backend Architecture

The backend is built with PHP and MySQL, following a modular architecture:

- `BaseModel.php` - Base class for database models
- `auth.php` - Authentication middleware
- `connect.php` - Database connection management
- `cors.php` - CORS configuration

### Key Features

- User authentication and authorization
- Recipe CRUD operations
- Category management
- Comments system
- Like/unlike functionality
- Audit logging
- Role-based access control (Admin/Editor/Viewer)

### API Endpoints

#### Users
- `POST /users/signup.php` - Register new user
- `POST /users/login.php` - User login
- `PUT /users/update_role.php` - Update user role

#### Recipes
- `GET /recipe/get.php` - Get recipe details
- `POST /recipe/post.php` - Create new recipe
- `PUT /recipe/put.php` - Update recipe
- `DELETE /recipe/delete.php` - Delete recipe

*More endpoints documented in the codebase*

## Frontend Features

Built with Create React App and includes:

- Responsive UI design
- User authentication flow
- Recipe browsing and search
- Category filtering
- User profiles
- Recipe management
- Comment system
- Like/unlike functionality

### Available Scripts

```bash
# Install dependencies
npm install

# Start development server
npm start

# Build for production
npm run build

## Contributors

### Core Team

- **Joy**
- **Yun**
- **Kaiki**
