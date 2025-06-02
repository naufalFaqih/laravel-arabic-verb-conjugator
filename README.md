# Tashrif Arab - Arabic Verb Conjugation App with AI Translation

<p align="center">
  <img src="public/img/logo am.png" width="200" alt="ArabicMorph Logo">
</p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

This Laravel 11 project is a comprehensive web application designed to help users with Arabic verb conjugation (Tashrif) and intelligent translation. It combines traditional Arabic grammar learning with modern AI-powered translation capabilities, providing an enhanced learning experience for Arabic language students and enthusiasts.

## ✨ Key Features

### 🔍 **Arabic Verb Conjugation**

-   **Advanced Verb Search**: Search for Arabic verbs with comprehensive conjugation results
-   **Complete Conjugation Display**: Shows past tense (madhi), present tense (mudhori), jussive (majzum), subjunctive (mansub), imperative (amar), and emphasized forms
-   **External API Integration**: Utilizes `http://qutrub.arabeyes.org/api` for accurate verb conjugation data
-   **Verb Classification**: Displays grammatical information including verb type (trilateral, transitive, etc.)

### 🤖 **AI-Powered Translation System**

-   **DeepSeek API Integration**: Advanced AI translation using DeepSeek language model
-   **Real-time Translation**: Automatic translation of Arabic text to Indonesian
-   **Smart Fallback System**: Comprehensive local dictionary for common Arabic verbs and grammar terms
-   **Contextual Translation**: Intelligent pattern recognition for different verb forms
-   **Grammar-Aware Translation**: Specialized translation for Arabic grammatical terms and verb conjugations

### 🔐 **User Authentication & Management**

-   **User Registration & Login**: Secure authentication system with robust password policies
-   **Session Management**: Persistent user sessions with login tracking
-   **Strong Password Policy**: Enforces minimum 8 characters with mixed case, numbers, and symbols

### 📚 **Search History & Analytics**

-   **Personal Search History**: Authenticated users can save and revisit their search queries
-   **History Management**: View, re-search, and delete individual entries or clear all history
-   **Search Analytics**: Track learning progress and frequently searched verbs

### 🎨 **Modern User Interface**

-   **Responsive Design**: Built with Blade templates and Tailwind CSS
-   **Arabic Text Support**: Proper RTL (Right-to-Left) text rendering
-   **Interactive Elements**: Dynamic search results with smooth animations
-   **Loading States**: Visual feedback during API calls and data processing
-   **Dark/Light Mode**: Adaptive theme support

### 🔧 **Advanced Technical Features**

-   **Caching System**: Intelligent caching for translations and search results
-   **Error Handling**: Comprehensive error handling and logging
-   **API Rate Limiting**: Smart request management for external APIs
-   **Performance Optimization**: Optimized database queries and asset loading

## 🚀 Installation

### Prerequisites

-   PHP 8.1 or higher
-   Composer
-   Node.js and npm
-   MySQL or SQLite database

### Setup Steps

1.  **Clone the repository:**

    ```bash
    git clone <repository_url>
    cd latihanLaravel11
    ```

2.  **Install Composer dependencies:**

    ```bash
    composer install
    ```

3.  **Install Node.js dependencies:**

    ```bash
    npm install
    ```

4.  **Environment Configuration:**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5.  **Configure your `.env` file:**

    ```env
    # Database Configuration
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=your_username
    DB_PASSWORD=your_password

    # DeepSeek API Configuration (for AI Translation)
    DEEPSEEK_API_KEY=your_deepseek_api_key_here
    DEEPSEEK_API_URL=https://api.deepseek.com/v1/chat/completions

    # External Arabic API
    QUTRUB_API_URL=http://qutrub.arabeyes.org/api
    ```

6.  **Database Setup:**

    ```bash
    php artisan migrate
    php artisan db:seed  # Optional: Creates test user
    ```

7.  **Asset Compilation:**

    ```bash
    npm run dev
    # For production: npm run build
    ```

8.  **Start Development Server:**
    ```bash
    php artisan serve
    ```

The application will be available at `http://127.0.0.1:8000`

## 📖 Usage Guide

### 🔍 **Basic Search**

1. Navigate to the homepage
2. Enter an Arabic verb (e.g., `ضرب`, `أكل`, `نظر`) in the search bar
3. Click "Tashrif" to get comprehensive conjugation results
4. View translations automatically generated for each verb form

### 🤖 **AI Translation Features**

-   **Automatic Translation**: All Arabic text is automatically translated to Indonesian
-   **Force Retranslation**: Use browser console commands to force fresh translations
-   **Translation History**: Cached translations for improved performance

### 👤 **User Account Features**

1. **Register**: Create an account with strong password requirements
2. **Login**: Access personal features and search history
3. **History Management**: View and manage your search history
4. **Profile**: Track your learning progress

### 🔧 **Advanced Features**

-   **API Testing**: Built-in tools for testing translation API functionality
-   **Cache Management**: Automatic cache optimization for better performance
-   **Debug Mode**: Console commands for advanced debugging

## 🏗️ Project Architecture

### **Core Controllers**

-   `ApiController.php`: External Arabic API integration
-   `TranslationController.php`: AI translation system with DeepSeek API
-   `SearchHistoryController.php`: User search history management
-   `Auth/LoginController.php` & `Auth/RegisterController.php`: Authentication

### **Models & Database**

-   `User.php`: User management with search history relationships
-   `SearchHistory.php`: Search query storage and retrieval
-   Database migrations for users, sessions, and search history

### **Frontend Assets**

-   `resources/js/search.js`: Dynamic search functionality
-   `resources/js/translation.js`: AI translation integration
-   `resources/js/search-history.js`: History management
-   `resources/css/app.css`: Tailwind CSS with Arabic text support

### **API Integration**

-   **Qutrub API**: Arabic verb conjugation data
-   **DeepSeek API**: AI-powered translation services
-   **Local Dictionary**: Fallback translation system

## 🔧 Configuration

### **Translation API Setup**

```env
# DeepSeek API for AI Translation
DEEPSEEK_API_KEY=sk-your-api-key-here
DEEPSEEK_API_URL=https://api.deepseek.com/v1/chat/completions
```

### **Cache Configuration**

```bash
# Clear caches when needed
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### **API Testing**

Test translation functionality in browser console:

```javascript
// Test translation API
fetch("/api/translate", {
    method: "POST",
    headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
            .content,
    },
    body: JSON.stringify({
        text: "أكل",
        source: "ar",
        target: "id",
        force: true,
    }),
})
    .then((r) => r.json())
    .then(console.log);
```

## 🤝 Contributing

We welcome contributions to improve the Arabic learning experience:

1. **Bug Reports**: Submit issues with detailed reproduction steps
2. **Feature Requests**: Suggest new learning features or improvements
3. **Translation Improvements**: Help enhance the Arabic-Indonesian dictionary
4. **Code Contributions**: Follow Laravel coding standards and include tests

### **Development Setup**

```bash
# Install development dependencies
composer install --dev
npm install

# Run tests
php artisan test

# Code formatting
./vendor/bin/pint
```

## 📝 API Endpoints

### **Translation API**

-   `POST /api/translate` - Translate Arabic text to Indonesian
-   `POST /api/check-api` - Check translation API status
-   `POST /api/batch-translate` - Batch translation for multiple texts

### **Verb Conjugation API**

-   `POST /api/search` - Search Arabic verb conjugations
-   `GET /api/history` - Get user search history

## 🔒 Security Features

-   **CSRF Protection**: All forms protected with CSRF tokens
-   **Input Validation**: Comprehensive input sanitization
-   **Authentication**: Secure user authentication with Laravel Sanctum
-   **API Rate Limiting**: Protection against API abuse
-   **Error Handling**: Secure error messages without sensitive data exposure

## 📊 Performance Features

-   **Response Caching**: Translation and search result caching
-   **Database Optimization**: Efficient queries with proper indexing
-   **Asset Optimization**: Minified CSS and JavaScript for production
-   **API Optimization**: Smart request batching and error handling

## 📱 Browser Support

-   **Modern Browsers**: Chrome 80+, Firefox 75+, Safari 13+, Edge 80+
-   **Arabic Text**: Full RTL (Right-to-Left) text support
-   **Responsive Design**: Mobile-first responsive layout
-   **Progressive Enhancement**: Graceful degradation for older browsers

## 🐛 Troubleshooting

### **Common Issues**

1. **Translation Not Working**: Check DeepSeek API key configuration
2. **Database Errors**: Verify database connection and run migrations
3. **Asset Loading Issues**: Run `npm run build` for production

### **Debug Commands**

```bash
# Check application status
php artisan about

# View logs
tail -f storage/logs/laravel.log

# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🙏 Acknowledgments

-   **Laravel Framework**: For the robust backend foundation
-   **Qutrub Project**: For Arabic verb conjugation data
-   **DeepSeek**: For AI-powered translation capabilities
-   **Tailwind CSS**: For modern, responsive styling
-   **Arabic Language Community**: For linguistic guidance and feedback

## 📞 Support

For support, bug reports, or feature requests:

-   **GitHub Issues**: Submit detailed issue reports
-   **Documentation**: Check the comprehensive inline documentation
-   **Community**: Join discussions in the project repository

---

**Happy Learning Arabic! 🌟**

_"اللغة العربية هي مفتاح الثقافة والمعرفة"_  
_"Arabic language is the key to culture and knowledge"_
