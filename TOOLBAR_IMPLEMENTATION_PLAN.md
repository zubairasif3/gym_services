# Toolbar Implementation Plan - FitScout

## Overview
This document outlines the step-by-step plan to implement the toolbar design shown in the client's image. The toolbar will display user-specific icons (Notifications, Messages, Followers, Profile) with badges for unread counts, along with a logout button.

## 🎯 Key Design Decision: Using Filament 3 + Laravel Notifications

**Important:** This implementation uses **Laravel's built-in notification system** which is fully compatible with **Filament 3**. This means:

✅ **Single Database Table:** One `notifications` table serves both web frontend and Filament admin panel  
✅ **Unified System:** Notifications sent from anywhere in the app appear in both places  
✅ **No Duplication:** No need for separate notification systems  
✅ **Filament Integration:** Filament automatically displays database notifications in the admin panel  
✅ **Web Integration:** Web frontend reads from the same database table  

**How it works:**
1. Create notification classes using Laravel's `Notification` class
2. Use `via(['database'])` to store notifications in database
3. Filament automatically reads and displays these notifications
4. Web frontend reads from `Auth::user()->notifications` relationship
5. Both sides use the same data structure and unread counts

## Current State Analysis

### Existing Features:
- ✅ User authentication system
- ✅ User profiles with avatar support (`avatar_url` field)
- ✅ Chat/messaging system (ChatRoom, ChatMessage models)
- ✅ Language toggle (English/Italian)
- ✅ Navigation menu (Home, About Us, Services, Contact Us)
- ✅ Search and Categories functionality

### Missing Features:
- ❌ Notifications system
- ❌ Followers/Following system
- ❌ Unread message count tracking
- ❌ User profile dropdown/control panel
- ❌ Online status indicator
- ❌ Icon badges for unread counts
- ❌ Toolbar icons UI components

---

## Implementation Steps

### Phase 1: Database Setup

#### Step 1.1: Create Laravel Notifications Table
**Command:** `php artisan notifications:table`

**File:** `database/migrations/YYYY_MM_DD_HHMMSS_create_notifications_table.php`

**Note:** Laravel's notification system uses a polymorphic relationship, so the table structure is:
- `id` (uuid, primary key)
- `type` (string) - Full class name of the notification (e.g., 'App\Notifications\NewMessage')
- `notifiable_type` (string) - Model class (usually 'App\Models\User')
- `notifiable_id` (bigint) - User ID
- `data` (json) - Notification data (title, message, action_url, etc.)
- `read_at` (timestamp, nullable)
- `created_at`, `updated_at`

**Benefits:**
- ✅ Works seamlessly with Filament 3 admin panel
- ✅ Works with Laravel's built-in notification system
- ✅ Supports multiple notifiable models (polymorphic)
- ✅ No custom model needed - use `Illuminate\Notifications\DatabaseNotification`

**Relationships:**
- Polymorphic relationship to User (or any notifiable model)

#### Step 1.2: Create Follows Table
**File:** `database/migrations/YYYY_MM_DD_HHMMSS_create_follows_table.php`

**Fields:**
- `id` (primary key)
- `follower_id` (foreign key to users) - user who follows
- `following_id` (foreign key to users) - user being followed
- `created_at`, `updated_at`
- Unique constraint on `[follower_id, following_id]`
- Prevent self-following

**Relationships:**
- Belongs to User (follower)
- Belongs to User (following)

#### Step 1.3: Add Read Field to Chat Messages
**File:** `database/migrations/YYYY_MM_DD_HHMMSS_add_read_to_chat_messages_table.php`

**Fields:**
- `read` (boolean, default: false)
- `read_at` (timestamp, nullable)

#### Step 1.4: Create User Sessions Table (for online status)
**File:** `database/migrations/YYYY_MM_DD_HHMMSS_create_user_sessions_table.php`

**Fields:**
- `id` (primary key)
- `user_id` (foreign key to users)
- `last_activity` (timestamp)
- `ip_address` (string, nullable)
- `user_agent` (text, nullable)

---

### Phase 2: Models & Relationships

#### Step 2.1: Use Laravel's DatabaseNotification Model
**File:** No custom model needed! Use `Illuminate\Notifications\DatabaseNotification`

**Laravel provides:**
- `notifiable()` - polymorphic relationship to User
- `read()` - check if read
- `markAsRead()` - mark as read
- `unread()` - scope for unread notifications

**Note:** We'll access notifications through the User model's `notifications` relationship.

#### Step 2.2: Create Follow Model
**File:** `app/Models/Follow.php`

**Methods:**
- `follower()` - belongsTo User (follower)
- `following()` - belongsTo User (following)
- `scopeFollowing($query, $userId)` - get users that a user follows
- `scopeFollowers($query, $userId)` - get users that follow a user

#### Step 2.3: Update User Model
**File:** `app/Models/User.php`

**Note:** User model already uses `Notifiable` trait, which provides:
- `notifications()` - hasMany DatabaseNotification (polymorphic)
- `unreadNotifications()` - hasMany DatabaseNotification (where read_at is null)
- `notify($notification)` - send notification
- `markNotificationAsRead($id)` - mark notification as read
- `markAllNotificationsAsRead()` - mark all as read

**Add Relationships:**
- `followers()` - hasMany Follow (where following_id = user_id)
- `following()` - hasMany Follow (where follower_id = user_id)
- `followingUsers()` - belongsToMany User through Follow
- `followerUsers()` - belongsToMany User through Follow

**Add Methods:**
- `getUnreadNotificationsCount()` - count unread notifications (uses `unreadNotifications()->count()`)
- `getUnreadMessagesCount()` - count unread messages
- `getAvatarOrInitials()` - return avatar URL or generate initials
- `isFollowing($userId)` - check if user is following another user
- `follow($userId)` - follow a user
- `unfollow($userId)` - unfollow a user

#### Step 2.4: Update ChatMessage Model
**File:** `app/Models/ChatMessage.php`

**Add Methods:**
- `markAsRead()` - mark message as read
- `scopeUnreadForUser($query, $userId)` - get unread messages for a user

#### Step 2.5: Update ChatRoom Model
**File:** `app/Models/ChatRoom.php`

**Add Methods:**
- `getUnreadMessagesCountForUser($userId)` - count unread messages in room for user
- `getOtherUser($userId)` - get the other user in the chat room

---

### Phase 2.5: Create Notification Classes (for both Web & Filament)

#### Step 2.5.1: Create Base Notification Classes
**Files:** `app/Notifications/`

**Notification Types to Create:**
1. **NewMessageNotification** - When user receives a new message
2. **NewFollowerNotification** - When someone follows the user
3. **GigUpdateNotification** - When a gig is updated/approved
4. **OrderNotification** - When an order is placed/updated

**Example: NewMessageNotification**
**File:** `app/Notifications/NewMessageNotification.php`

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;
    protected $sender;

    public function __construct($message, $sender)
    {
        $this->message = $message;
        $this->sender = $sender;
    }

    public function via($notifiable)
    {
        return ['database', 'mail']; // Database for web & Filament, Mail optional
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Message',
            'message' => $this->sender->name . ' sent you a message',
            'action_url' => route('chat'),
            'icon' => 'envelope',
            'type' => 'message',
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Message on FitScout')
            ->line($this->sender->name . ' sent you a message')
            ->action('View Message', route('chat'));
    }

    // This method allows Filament to display the notification
    public function toFilament($notifiable)
    {
        return FilamentNotification::make()
            ->title('New Message')
            ->body($this->sender->name . ' sent you a message')
            ->success()
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label('View Message')
                    ->url(route('chat'))
            ]);
    }
}
```

**Key Points:**
- Use `via(['database'])` to store in database (works for both web and Filament)
- `toDatabase()` method returns array that will be stored in `data` column
- Filament automatically reads from database notifications
- Web frontend will read from same database table
- Can add `toFilament()` for custom Filament display (optional)

#### Step 2.5.2: Sending Notifications
**Usage Examples:**

```php
// In your controllers/services
use App\Notifications\NewMessageNotification;

// Send notification
$user->notify(new NewMessageNotification($message, $sender));

// Or using Filament's notification system (in admin panel)
use Filament\Notifications\Notification as FilamentNotification;

FilamentNotification::make()
    ->title('Success')
    ->body('User has been notified')
    ->success()
    ->send();

// This will also store in database if user is logged in
```

---

### Phase 3: Controllers

#### Step 3.1: Create NotificationController
**File:** `app/Http/Controllers/NotificationController.php`

**Methods:**
- `index()` - get all notifications (paginated) - uses `Auth::user()->notifications`
- `unread()` - get unread notifications - uses `Auth::user()->unreadNotifications`
- `markAsRead($id)` - mark single notification as read - uses `Auth::user()->notifications()->find($id)->markAsRead()`
- `markAllAsRead()` - mark all notifications as read - uses `Auth::user()->unreadNotifications->markAsRead()`
- `getUnreadCount()` - get count of unread notifications (AJAX endpoint) - uses `Auth::user()->unreadNotifications->count()`

**Example Implementation:**
```php
public function getUnreadCount()
{
    return response()->json([
        'count' => Auth::user()->unreadNotifications->count()
    ]);
}

public function index()
{
    $notifications = Auth::user()->notifications()->paginate(20);
    return view('web.notifications.index', compact('notifications'));
}

public function markAsRead($id)
{
    $notification = Auth::user()->notifications()->findOrFail($id);
    $notification->markAsRead();
    return response()->json(['success' => true]);
}
```

#### Step 3.2: Create FollowController
**File:** `app/Http/Controllers/FollowController.php`

**Methods:**
- `follow($userId)` - follow a user
- `unfollow($userId)` - unfollow a user
- `following()` - get list of users being followed
- `followers()` - get list of followers
- `check($userId)` - check if following a user (AJAX)

#### Step 3.3: Create UserProfileController (if not exists)
**File:** `app/Http/Controllers/UserProfileController.php`

**Methods:**
- `show()` - show user profile
- `edit()` - show edit form
- `update()` - update profile
- `updateAvatar()` - update profile picture

---

### Phase 4: Routes

#### Step 4.1: Add Routes to web.php
**File:** `routes/web.php`

```php
// Notifications routes
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    
    // Follow routes
    Route::post('/users/{user}/follow', [FollowController::class, 'follow'])->name('users.follow');
    Route::delete('/users/{user}/unfollow', [FollowController::class, 'unfollow'])->name('users.unfollow');
    Route::get('/following', [FollowController::class, 'following'])->name('following.index');
    Route::get('/followers', [FollowController::class, 'followers'])->name('followers.index');
    
    // Profile routes
    Route::get('/profile', [UserProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [UserProfileController::class, 'update'])->name('profile.update');
    
    // Messages unread count
    Route::get('/messages/unread-count', [ChatController::class, 'getUnreadCount'])->name('messages.unread-count');
});
```

---

### Phase 5: Views & UI Components

#### Step 5.1: Update Header Layout
**File:** `resources/views/web/layouts/app.blade.php`

**Changes:**
- Replace guest/authenticated section with new toolbar icons
- Add icons for: Notifications, Messages, Followers, Profile
- Add badges for unread counts
- Add dropdown menus for each icon
- Add user profile dropdown with edit profile and logout

**Icon Structure (when authenticated):**
```html
<div class="user-toolbar-icons">
    <!-- Notifications Icon -->
    <a href="#" class="toolbar-icon notifications-icon" data-bs-toggle="dropdown">
        <i class="far fa-bell"></i>
        <span class="badge" id="notification-badge">0</span>
    </a>
    <div class="dropdown-menu notifications-dropdown">...</div>
    
    <!-- Messages Icon -->
    <a href="{{ route('chat') }}" class="toolbar-icon messages-icon">
        <i class="far fa-envelope"></i>
        <span class="badge" id="message-badge">0</span>
    </a>
    
    <!-- Followers Icon -->
    <a href="{{ route('following.index') }}" class="toolbar-icon followers-icon">
        <i class="far fa-heart"></i>
    </a>
    
    <!-- User Profile Icon -->
    <div class="dropdown profile-dropdown">
        <a href="#" class="toolbar-icon profile-icon" data-bs-toggle="dropdown">
            <img src="{{ Auth::user()->getAvatarOrInitials() }}" alt="Profile" class="profile-avatar">
            <span class="online-indicator"></span>
        </a>
        <div class="dropdown-menu profile-menu">
            <a href="{{ route('profile.show') }}">View Profile</a>
            <a href="{{ route('profile.edit') }}">Edit Profile</a>
            <hr>
            <form method="POST" action="{{ route('web.logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>
</div>
```

#### Step 5.2: Create Notifications Dropdown Component
**File:** `resources/views/components/notifications-dropdown.blade.php`

**Features:**
- List of recent notifications
- Mark as read on click
- "Mark all as read" button
- Link to full notifications page
- Empty state message

#### Step 5.3: Create Followers Page
**File:** `resources/views/web/following/index.blade.php`

**Features:**
- Grid/list of followed professionals
- Profile picture or initials
- Name/username
- Click to view profile
- Unfollow button
- Empty state

#### Step 5.4: Create Profile Edit Page
**File:** `resources/views/web/profile/edit.blade.php`

**Features:**
- Edit profile information
- Upload/change profile picture
- Change password
- Update email
- Similar to Fiverr's profile edit

---

### Phase 6: CSS Styling

#### Step 6.1: Create Toolbar Styles
**File:** `public/web/css/toolbar.css` (or add to existing style.css)

**Styles Needed:**
- `.user-toolbar-icons` - container for icons
- `.toolbar-icon` - base icon style
- `.badge` - unread count badge (red dot with number)
- `.notifications-dropdown` - dropdown menu styling
- `.profile-avatar` - circular avatar image
- `.online-indicator` - green dot for online status
- `.profile-menu` - profile dropdown menu
- Hover effects
- Responsive design for mobile

**Key Design Elements:**
- Black toolbar background
- White/light icons
- Red badges for unread counts
- Green dot for online status
- Smooth transitions and hover effects

---

### Phase 7: JavaScript Functionality

#### Step 7.1: Create Toolbar JavaScript
**File:** `public/web/js/toolbar.js`

**Features:**
- AJAX calls to update unread counts
- Real-time notification updates (polling or WebSocket)
- Mark notifications as read on click
- Auto-refresh unread counts every 30 seconds
- Handle dropdown interactions

**Functions:**
```javascript
// Update unread counts
function updateUnreadCounts() {
    // Fetch notification count
    // Fetch message count
    // Update badges
}

// Mark notification as read
function markNotificationAsRead(notificationId) {
    // AJAX call to mark as read
    // Update UI
}

// Initialize toolbar
function initToolbar() {
    updateUnreadCounts();
    setInterval(updateUnreadCounts, 30000); // Update every 30 seconds
}
```

#### Step 7.2: Add to Layout
**File:** `resources/views/web/layouts/app.blade.php`

Add script tag to include toolbar.js and initialize on page load.

---

### Phase 8: Helper Functions & Utilities

#### Step 8.1: Create Avatar Helper
**File:** `app/Helpers/AvatarHelper.php` or add to User model

**Function:**
```php
public function getAvatarOrInitials()
{
    if ($this->avatar_url && Storage::exists('public/' . $this->avatar_url)) {
        return asset('storage/' . $this->avatar_url);
    }
    
    // Generate initials
    $initials = strtoupper(substr($this->name, 0, 1));
    if ($this->surname) {
        $initials .= strtoupper(substr($this->surname, 0, 1));
    }
    
    // Return SVG or use a service like UI Avatars
    return 'https://ui-avatars.com/api/?name=' . urlencode($this->name . ' ' . $this->surname) . '&background=00b3f1&color=fff&size=40';
}
```

#### Step 8.2: Online Status Tracking
**File:** `app/Http/Middleware/UpdateUserActivity.php`

**Purpose:**
- Track user's last activity
- Update user_sessions table
- Determine online status (active within last 5 minutes)

---

### Phase 9: Testing & Refinement

#### Step 9.1: Test All Features
- ✅ Notifications creation and display
- ✅ Unread count badges
- ✅ Mark as read functionality
- ✅ Follow/unfollow functionality
- ✅ Followers page display
- ✅ Profile dropdown
- ✅ Avatar display (with and without image)
- ✅ Online status indicator
- ✅ Unread message count
- ✅ Responsive design

#### Step 9.2: Mobile Responsive Design
- Ensure toolbar icons work on mobile
- Consider hamburger menu for mobile
- Test dropdowns on touch devices

#### Step 9.3: Performance Optimization
- Cache unread counts
- Optimize database queries
- Lazy load notifications dropdown

---

## File Structure Summary

```
app/
├── Http/
│   └── Controllers/
│       ├── NotificationController.php (NEW)
│       ├── FollowController.php (NEW)
│       └── UserProfileController.php (NEW or UPDATE)
├── Models/
│   ├── Follow.php (NEW)
│   ├── User.php (UPDATE - already has Notifiable trait)
│   ├── ChatMessage.php (UPDATE)
│   └── ChatRoom.php (UPDATE)
├── Notifications/
│   ├── NewMessageNotification.php (NEW)
│   ├── NewFollowerNotification.php (NEW)
│   ├── GigUpdateNotification.php (NEW)
│   └── OrderNotification.php (NEW - optional)
└── Helpers/
    └── AvatarHelper.php (NEW - optional)

database/
└── migrations/
    ├── YYYY_MM_DD_HHMMSS_create_notifications_table.php (NEW - use: php artisan notifications:table)
    ├── YYYY_MM_DD_HHMMSS_create_follows_table.php (NEW)
    ├── YYYY_MM_DD_HHMMSS_add_read_to_chat_messages_table.php (NEW)
    └── YYYY_MM_DD_HHMMSS_create_user_sessions_table.php (NEW)

resources/
└── views/
    ├── web/
    │   ├── layouts/
    │   │   └── app.blade.php (UPDATE)
    │   ├── following/
    │   │   └── index.blade.php (NEW)
    │   └── profile/
    │       ├── show.blade.php (NEW)
    │       └── edit.blade.php (NEW)
    └── components/
        └── notifications-dropdown.blade.php (NEW)

public/
└── web/
    ├── css/
    │   └── toolbar.css (NEW or add to style.css)
    └── js/
        └── toolbar.js (NEW)

routes/
└── web.php (UPDATE)
```

---

## Implementation Priority

1. **High Priority (Core Functionality):**
   - Database migrations
   - Models and relationships
   - Basic controllers
   - Header UI update with icons
   - Unread count badges

2. **Medium Priority (User Experience):**
   - Notifications dropdown
   - Followers page
   - Profile dropdown
   - Avatar display

3. **Low Priority (Enhancements):**
   - Real-time updates
   - Online status
   - Advanced styling
   - Performance optimizations

---

## Notes

### Using Filament 3 Notifications:

1. **Database Notifications (Recommended):**
   - Use `via(['database'])` in notification classes
   - Notifications automatically appear in Filament admin panel
   - Same notifications accessible in web frontend
   - No additional setup needed

2. **Sending Notifications:**
   ```php
   // From anywhere in your app
   $user->notify(new NewMessageNotification($message, $sender));
   
   // In Filament resources/actions
   Notification::make()
       ->title('Success')
       ->success()
       ->sendToDatabase($user); // This stores in database
   ```

3. **Displaying in Filament:**
   - Filament automatically shows database notifications in the admin panel
   - Users see notifications in the Filament header
   - No custom code needed for Filament side

4. **Displaying in Web Frontend:**
   - Read from `Auth::user()->notifications` or `Auth::user()->unreadNotifications`
   - Create custom UI components to display notifications
   - Use same data structure from `toDatabase()` method

5. **Benefits:**
   - ✅ Single source of truth (database)
   - ✅ Works in both web and Filament
   - ✅ Laravel's built-in features (queues, mail, etc.)
   - ✅ No custom notification model needed
   - ✅ Polymorphic support (can notify any model)

### Other Notes:

- Use Font Awesome icons (already included: `flaticon.css`, `fontawesome.css`)
- Follow existing code style and patterns
- Ensure all routes are protected with `auth` middleware
- Test with different user types (Customer, Professional, Admin)
- For real-time updates, consider Laravel Echo + Pusher or WebSockets
- Use Laravel's notification events for real-time updates if needed

---

## Estimated Timeline

- Phase 1-2: Database & Models (2-3 hours)
- Phase 3-4: Controllers & Routes (2-3 hours)
- Phase 5: Views & UI (4-5 hours)
- Phase 6: CSS Styling (2-3 hours)
- Phase 7: JavaScript (2-3 hours)
- Phase 8-9: Helpers & Testing (2-3 hours)

**Total Estimated Time: 14-20 hours**

---

## Next Steps

1. Review and approve this plan
2. Start with Phase 1 (Database Setup)
3. Implement incrementally, testing each phase
4. Get client feedback after Phase 5 (UI implementation)

