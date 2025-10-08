<?php
// index.php - Router chính

// Thiết lập múi giờ Việt Nam - SỬA LỖI MÚI GIỜ
date_default_timezone_set('Asia/Ho_Chi_Minh');

require_once 'controllers/AuthController.php';
require_once 'controllers/PregnancyController.php';
require_once 'controllers/FamilyController.php';

$action = $_GET['action'] ?? 'index';

// Routing
switch($action) {
    // Auth routes
    case 'index':
        $controller = new AuthController();
        $controller->index();
        break;
    
    case 'select_role':
        $controller = new AuthController();
        $controller->selectRole();
        break;
    
    case 'register_form':
        $controller = new AuthController();
        $controller->registerForm();
        break;
    
    case 'send_otp':
        $controller = new AuthController();
        $controller->sendOTP();
        break;
    
    case 'verify_otp':
        $controller = new AuthController();
        $controller->verifyOTP();
        break;
    
    case 'register':
        $controller = new AuthController();
        $controller->register();
        break;
    
    case 'login':
        $controller = new AuthController();
        $controller->login();
        break;
    
    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;
    
    // Pregnancy routes
    case 'pregnancy_info':
        $controller = new PregnancyController();
        $controller->pregnancyInfo();
        break;
    
    case 'save_pregnancy_info':
        $controller = new PregnancyController();
        $controller->savePregnancyInfo();
        break;
    
    case 'pregnancy_dashboard':
        $controller = new PregnancyController();
        $controller->dashboard();
        break;
    
    case 'weekly_tracker':
        $controller = new PregnancyController();
        $controller->weeklyTracker();
        break;
    
    // Family routes
    case 'search_pregnant':
        $controller = new FamilyController();
        $controller->searchPregnant();
        break;
    
    case 'find_pregnant':
        $controller = new FamilyController();
        $controller->findPregnant();
        break;
    
    case 'connect_pregnant':
        $controller = new FamilyController();
        $controller->connectPregnant();
        break;
    
    case 'family_dashboard':
        $controller = new FamilyController();
        $controller->dashboard();
        break;
    
    // Notification routes
    case 'notifications':
        require_once 'controllers/NotificationController.php';
        $controller = new NotificationController();
        $controller->index();
        break;
    
    case 'accept_connection':
        require_once 'controllers/NotificationController.php';
        $controller = new NotificationController();
        $controller->acceptConnection();
        break;
    
    case 'reject_connection':
        require_once 'controllers/NotificationController.php';
        $controller = new NotificationController();
        $controller->rejectConnection();
        break;
    
    case 'count_unread_notifications':
        require_once 'controllers/NotificationController.php';
        $controller = new NotificationController();
        $controller->countUnread();
        break;
    
    // Post routes
    case 'newsfeed':
        require_once 'controllers/PostController.php';
        $controller = new PostController();
        $controller->newsfeed();
        break;
    
    case 'create_post':
        require_once 'controllers/PostController.php';
        $controller = new PostController();
        $controller->create();
        break;
    
    case 'like_post':
        require_once 'controllers/PostController.php';
        $controller = new PostController();
        $controller->like();
        break;
    
    case 'unlike_post':
        require_once 'controllers/PostController.php';
        $controller = new PostController();
        $controller->unlike();
        break;
    
    case 'add_comment':
        require_once 'controllers/PostController.php';
        $controller = new PostController();
        $controller->comment();
        break;
    
    case 'get_comments':
        require_once 'controllers/PostController.php';
        $controller = new PostController();
        $controller->getComments();
        break;
    
    case 'share_post':
        require_once 'controllers/PostController.php';
        $controller = new PostController();
        $controller->share();
        break;
    
    case 'delete_post':
        require_once 'controllers/PostController.php';
        $controller = new PostController();
        $controller->delete();
        break;
        
    
    default:
        header('HTTP/1.0 404 Not Found');
        echo '404 - Page Not Found';
        break;
}
?>