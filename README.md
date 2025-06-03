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
-   **Comprehensive Data Display**: 8 different conjugation categories with pronoun mappings
-   **Smart Search Summary**: Quick overview of madhi, mudhori, and amar forms

### 🤖 **AI-Powered Translation System**

-   **DeepSeek API Integration**: Advanced AI translation using DeepSeek language model with API key `sk-86cd11de25dc45a3920647b89c398c75`
-   **Real-time Translation**: Automatic translation of Arabic text to Indonesian
-   **Smart Fallback System**: Comprehensive local dictionary for common Arabic verbs and grammar terms
-   **Contextual Translation**: Intelligent pattern recognition for different verb forms
-   **Grammar-Aware Translation**: Specialized translation for Arabic grammatical terms and verb conjugations
-   **Enhanced Caching**: Multi-layer caching system with memory cache and localStorage
-   **Translation Validation**: Robust validation to ensure translation quality
-   **Batch Translation**: Support for translating multiple texts simultaneously
-   **Force Retranslation**: Ability to bypass cache and get fresh translations

### 🔐 **User Authentication & Management**

-   **User Registration & Login**: Secure authentication system with robust password policies
-   **Session Management**: Persistent user sessions with login tracking
-   **Strong Password Policy**: Enforces minimum 8 characters with mixed case, numbers, and symbols
-   **Last Login Tracking**: Track user's last login time for security monitoring
-   **Admin Panel**: Complete admin dashboard for user management

### 📚 **Search History & Analytics**

-   **Personal Search History**: Authenticated users can save and revisit their search queries
-   **History Management**: View, re-search, and delete individual entries or clear all history
-   **Search Analytics**: Track learning progress and frequently searched verbs
-   **Smart Duplicate Prevention**: Prevent duplicate saves within 1-minute window
-   **Recent History Display**: Show last 5 searches on homepage
-   **Export/Import**: Manage search history efficiently

### 🎨 **Modern User Interface**

-   **Responsive Design**: Built with Blade templates and Tailwind CSS
-   **Arabic Text Support**: Proper RTL (Right-to-Left) text rendering
-   **Interactive Elements**: Dynamic search results with smooth animations
-   **Loading States**: Visual feedback during API calls and data processing
-   **Mobile-First Design**: Optimized for mobile devices with touch-friendly controls
-   **Horizontal Scrolling**: Responsive table layout with smooth horizontal scrolling
-   **Custom Scrollbars**: Styled scrollbars for better user experience
-   **Scroll Indicators**: Visual hints for scrollable content on mobile

### 🔧 **Advanced Technical Features**

-   **Intelligent Caching System**: Multi-layer caching for translations and search results
-   **Error Handling**: Comprehensive error handling and logging
-   **API Rate Limiting**: Smart request management for external APIs
-   **Performance Optimization**: Optimized database queries and asset loading
-   **Debug Tools**: Built-in debugging tools for translation testing
-   **Translation Debug Mode**: Console tools for testing translation functionality
-   **Cache Management**: Automatic cache cleanup and optimization

### 👨‍💼 **Admin Features**

-   **User Management**: Complete user management with admin controls
-   **User Statistics**: Track user activity and search patterns
-   **Admin Dashboard**: Comprehensive overview of system statistics
-   **User Detail Views**: Detailed user information and search history
-   **Admin Toggle**: Promote/demote users to admin status
-   **System Monitoring**: Monitor system health and performance
-   **Telescope Integration**: Laravel Telescope for debugging and monitoring

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
    DEEPSEEK_API_KEY=sk-86cd11de25dc45a3920647b89c398c75
    DEEPSEEK_API_URL=https://api.deepseek.com/v1/chat/completions

    # External Arabic API
    QUTRUB_API_URL=http://qutrub.arabeyes.org/api

    # Chat API Configuration
    CHAT_API_KEY=sk-86cd11de25dc45a3920647b89c398c75
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
2. Enter an Arabic verb (e.g., `ضرب`, `أكل`, `نظر`, `نَظَرَ`) in the search bar
3. Click "Tashrif" to get comprehensive conjugation results
4. View translations automatically generated for each verb form
5. Use horizontal scroll on mobile devices to view all conjugation forms

### 🤖 **AI Translation Features**

-   **Automatic Translation**: All Arabic text is automatically translated to Indonesian using DeepSeek API
-   **Force Retranslation**: Use browser console commands to force fresh translations
-   **Translation History**: Cached translations for improved performance
-   **Local Fallback**: Comprehensive local dictionary when API is unavailable
-   **Grammar-Aware**: Specialized translations for Arabic grammatical terms

### 👤 **User Account Features**

1. **Register**: Create an account with strong password requirements
2. **Login**: Access personal features and search history
3. **History Management**: View and manage your search history
4. **Profile**: Track your learning progress
5. **Search Tracking**: Automatic saving of search queries for registered users

### 📱 **Mobile Experience**

-   **Touch-Friendly**: Optimized touch controls for mobile devices
-   **Horizontal Scrolling**: Smooth scrolling for conjugation tables
-   **Responsive Layout**: Adapts to different screen sizes
-   **Visual Indicators**: Clear indicators for scrollable content

### 🔧 **Advanced Features**

-   **API Testing**: Built-in tools for testing translation API functionality
-   **Cache Management**: Automatic cache optimization for better performance
-   **Debug Mode**: Console commands for advanced debugging
-   **Translation Testing**: Test specific translations via browser console

### 👨‍💼 **Admin Features** (For Admin Users)

1. **Access Admin Panel**: Navigate to `/admin/dashboard`
2. **User Management**: View and manage all users
3. **Search Monitoring**: Monitor user search activities
4. **System Health**: Check system performance and status
5. **User Promotion**: Promote users to admin status

## 🏗️ Project Architecture

### **Core Controllers**

-   [`ApiController.php`](app/Http/Controllers/ApiController.php): External Arabic API integration
-   [`TranslationController.php`](app/Http/Controllers/TranslationController.php): AI translation system with DeepSeek API
-   [`SearchHistoryController.php`](app/Http/Controllers/SearchHistoryController.php): User search history management
-   [`Auth/LoginController.php`](app/Http/Controllers/Auth/LoginController.php) & [`Auth/RegisterController.php`](app/Http/Controllers/Auth/RegisterController.php): Authentication
-   [`AdminController.php`](app/Http/Controllers/AdminController.php): Admin panel functionality
-   [`ChatController.php`](app/Http/Controllers/ChatController.php): AI chat integration

### **Models & Database**

-   [`User.php`](app/Models/User.php): User management with search history relationships and admin features
-   [`SearchHistory.php`](app/Models/SearchHistory.php): Search query storage and retrieval
-   Database migrations for users, sessions, search history, and admin features

### **Frontend Assets**

-   [`resources/js/search.js`](resources/js/search.js): Dynamic search functionality with responsive design
-   [`resources/js/translation.js`](resources/js/translation.js): AI translation integration with DeepSeek API
-   [`resources/js/search-history.js`](resources/js/search-history.js): History management
-   [`resources/js/translation-debug.js`](resources/js/translation-debug.js): Debug tools for translation testing
-   [`resources/css/app.css`](resources/css/app.css): Tailwind CSS with Arabic text support and responsive scrolling

### **API Integration**

-   **Qutrub API**: Arabic verb conjugation data from `http://qutrub.arabeyes.org/api`
-   **DeepSeek API**: AI-powered translation services using `https://api.deepseek.com/v1/chat/completions`
-   **Local Dictionary**: Comprehensive fallback translation system
-   **Chat API**: AI chat functionality using DeepSeek

### **Views & Components**

-   [`resources/views/home.blade.php`](resources/views/home.blade.php): Main search interface with responsive design
-   [`resources/views/history.blade.php`](resources/views/history.blade.php): Search history management
-   [`resources/views/admin/`](resources/views/admin/): Admin panel views
-   [`resources/views/components/layout.blade.php`](resources/views/components/layout.blade.php): Main layout component
-   [`resources/views/auth/`](resources/views/auth/): Authentication views

## 🔧 Configuration

### **Translation API Setup**

```env
# DeepSeek API for AI Translation
DEEPSEEK_API_KEY=sk-86cd11de25dc45a3920647b89c398c75
DEEPSEEK_API_URL=https://api.deepseek.com/v1/chat/completions
```

### **Cache Configuration**

```bash
# Clear caches when needed
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### **API Testing**

Test translation functionality in browser console:

```javascript
// Test DeepSeek translation API
window.debugDeepSeek.testAPI();

// Test specific translation
window.debugDeepSeek.testTranslation("نَظَرَ");

// Force retranslate all elements
window.TranslationEnhanced.forceRetranslate();

// Clear translation cache
window.TranslationEnhanced.clearCache();

// Test translation API directly
fetch("/api/translate", {
    method: "POST",
    headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
            .content,
    },
    body: JSON.stringify({
        text: "نَظَرَ",
        source: "ar",
        target: "id",
        force: true,
    }),
})
    .then((r) => r.json())
    .then(console.log);
```

### **Debug Commands**

```javascript
// Available debug functions
window.debugDeepSeek = {
    testAPI: () => {}, // Test API connectivity
    testTranslation: (text) => {}, // Test specific translation
    force: () => {}, // Force retranslation
    clear: () => {}, // Clear cache
};
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

# Asset watching for development
npm run dev
```

### **Translation System Development**

-   Translations are handled by [`TranslationController.php`](app/Http/Controllers/TranslationController.php)
-   Frontend integration in [`resources/js/translation.js`](resources/js/translation.js)
-   Local dictionary can be expanded in the `getLocalTranslation()` method
-   Debug tools available in [`resources/js/translation-debug.js`](resources/js/translation-debug.js)

## 📝 API Endpoints

### **Translation API**

-   `POST /api/translate` - Translate Arabic text to Indonesian using DeepSeek API
-   `POST /api/translate/check` - Check DeepSeek API status and connectivity
-   `POST /api/translate/batch` - Batch translation for multiple texts
-   `POST /translation/translate` - Alternative translation endpoint
-   `POST /translation/check-api` - Alternative API check endpoint

### **Verb Conjugation API**

-   `GET /api/search-verb` - Search Arabic verb conjugations
-   `GET /search-verb` - Alternative search endpoint

### **User Management API**

-   `GET /history` - Get user search history
-   `POST /history` - Save search to history
-   `DELETE /history/{id}` - Delete specific search history
-   `DELETE /history` - Clear all search history

### **Admin API**

-   `GET /admin/dashboard` - Admin dashboard
-   `GET /admin/users` - User management
-   `GET /admin/users/{id}` - User detail view
-   `PATCH /admin/users/{id}/toggle-admin` - Toggle admin status
-   `POST /admin/clear-cache` - Clear system cache
-   `POST /admin/optimize` - Optimize system

### **Chat API**

-   `POST /chat` - AI chat using DeepSeek API (CSRF exempt)

## 🔒 Security Features

-   **CSRF Protection**: All forms protected with CSRF tokens
-   **Input Validation**: Comprehensive input sanitization with Arabic text validation
-   **Authentication**: Secure user authentication with Laravel's built-in system
-   **API Rate Limiting**: Protection against API abuse
-   **Error Handling**: Secure error messages without sensitive data exposure
-   **Admin Middleware**: Separate admin access control
-   **Password Policies**: Strong password requirements with validation
-   **Session Security**: Secure session management with regeneration

## 📊 Performance Features

-   **Multi-Layer Caching**: Translation and search result caching with memory and localStorage
-   **Database Optimization**: Efficient queries with proper indexing
-   **Asset Optimization**: Minified CSS and JavaScript for production
-   **API Optimization**: Smart request batching and error handling
-   **Lazy Loading**: Optimized loading of translation data
-   **Response Optimization**: Compressed responses and optimized JSON
-   **Cache Invalidation**: Smart cache invalidation strategies

## 📱 Browser Support & Responsive Design

-   **Modern Browsers**: Chrome 80+, Firefox 75+, Safari 13+, Edge 80+
-   **Arabic Text**: Full RTL (Right-to-Left) text support
-   **Responsive Design**: Mobile-first responsive layout
-   **Progressive Enhancement**: Graceful degradation for older browsers
-   **Touch Support**: Optimized touch interactions for mobile devices
-   **Horizontal Scrolling**: Smooth horizontal scrolling for tables
-   **Custom Scrollbars**: Styled scrollbars for better user experience
-   **Scroll Indicators**: Visual indicators for scrollable content

### **Mobile Features**

-   **Touch-Friendly Controls**: Optimized for touch interaction
-   **Scroll Hints**: Automatic scroll hints for mobile users
-   **Responsive Tables**: Horizontal scrolling conjugation tables
-   **Mobile Navigation**: Collapsible navigation for mobile devices
-   **Touch Scrolling**: Enhanced touch scrolling with momentum

## 🐛 Troubleshooting

### **Common Issues**

1. **Translation Not Working**:

    - Check DeepSeek API key configuration in `.env`
    - Verify API key: `sk-86cd11de25dc45a3920647b89c398c75`
    - Test API connectivity using `window.debugDeepSeek.testAPI()`

2. **Database Errors**:

    - Verify database connection and run migrations
    - Check search_histories table exists: `php artisan migrate`

3. **Asset Loading Issues**:

    - Run `npm run build` for production
    - Clear browser cache
    - Check Vite configuration

4. **Horizontal Scrolling Issues**:
    - Check CSS for overflow-x-auto classes
    - Verify min-w-max classes on scroll containers
    - Test on different screen sizes

### **Debug Commands**

```bash
# Check application status
php artisan about

# View logs
tail -f storage/logs/laravel.log

# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check translation system
php artisan tinker
>>> app(App\Http\Controllers\TranslationController::class)->checkApi();
```

### **Browser Console Debugging**

```javascript
// Check translation system
window.debugDeepSeek.testAPI();

// Test specific word
window.debugDeepSeek.testTranslation("نَظَرَ");

// Force refresh translations
window.TranslationEnhanced.forceRetranslate();

// Check available debug functions
console.log(window.debugDeepSeek);
console.log(window.TranslationEnhanced);
```

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🙏 Acknowledgments

-   **Laravel Framework**: For the robust backend foundation
-   **Qutrub Project**: For Arabic verb conjugation data and API access
-   **DeepSeek**: For AI-powered translation capabilities and language model
-   **Tailwind CSS**: For modern, responsive styling and utility classes
-   **Arabic Language Community**: For linguistic guidance and feedback
-   **Laravel Telescope**: For debugging and monitoring capabilities
-   **Vite**: For modern asset compilation and hot reloading

## 📞 Support

For support, bug reports, or feature requests:

-   **GitHub Issues**: Submit detailed issue reports with reproduction steps
-   **Documentation**: Check the comprehensive inline documentation
-   **Community**: Join discussions in the project repository
-   **Translation Issues**: Report translation quality issues for improvement

### **Getting Help**

1. **Check Documentation**: Review this README and inline code comments
2. **Use Debug Tools**: Utilize built-in debug functions for troubleshooting
3. **Check Logs**: Review Laravel logs for error details
4. **Test APIs**: Use browser console tools to test API connectivity

---

**Happy Learning Arabic! 🌟**

_"اللغة العربية هي مفتاح الثقافة والمعرفة"_  
_"Arabic language is the key to culture and knowledge"_

---

## 📋 Feature Changelog

### **v2.0 - Enhanced AI Translation & Responsive Design**

-   ✅ DeepSeek API integration for AI translation
-   ✅ Responsive horizontal scrolling for conjugation tables
-   ✅ Enhanced caching system with multi-layer support
-   ✅ Mobile-first responsive design improvements
-   ✅ Debug tools for translation testing
-   ✅ Admin panel for user management
-   ✅ Improved search history management
-   ✅ Touch-friendly mobile interface
-   ✅ Custom scrollbar styling
-   ✅ Translation validation and error handling

### **v1.0 - Core Features**

-   ✅ Basic Arabic verb conjugation
-   ✅ User authentication system
-   ✅ Search history functionality
-   ✅ External API integration
-   ✅ Basic translation features
