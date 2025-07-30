# Agora Chat Setup Guide

## Prerequisites

1. **Agora Console Account**
   - Go to https://console.agora.io
   - Sign up and create an account
   - Verify your email

2. **Create Agora Chat Project**
   - Login to Agora Console
   - Click "Create" to create a new project
   - Select "Chat" as project type
   - Give your project a name (e.g., "Fitness Chat App")
   - Click "Create"

3. **Get Credentials**
   - In your project dashboard, note down:
     - Organization Name
     - App Name
   - Go to "Chat" → "Credentials" tab
   - Copy Client ID and Client Secret

## Environment Configuration

Add these variables to your `.env` file:

```env
# Agora Chat Credentials
AGORA_CHAT_ORG_NAME=your_org_name
AGORA_CHAT_APP_NAME=your_app_name
AGORA_CHAT_CLIENT_ID=your_client_id
AGORA_CHAT_CLIENT_SECRET=your_client_secret
AGORA_CHAT_BASE_URL=https://a41.easemob.com
```

## Database Migration

Run the migration to add Agora Chat username field:

```bash
php artisan migrate
```

## API Endpoints

### 1. Test Connection
```
GET /api/chat/test
```
Tests if Agora Chat credentials are working correctly.

### 2. Register User
```
POST /api/chat/register
```
Manually register a user in Agora Chat.

**Request Body:**
```json
{
    "username": "user_123",
    "password": "secure_password",
    "nickname": "John Doe",
    "avatar": "https://example.com/avatar.jpg"
}
```

### 3. Auto Register User
```
POST /api/chat/auto-register
```
Automatically register the authenticated user in Agora Chat.

### 4. Get Chat Token
```
GET /api/chat/token
```
Get chat token for the authenticated user.

### 5. Get Chat Users
```
GET /api/chat/users
```
Get list of all users available for chat.

### 6. Get User Info
```
GET /api/chat/user?username=user_123
```
Get information about a specific user.

### 7. Delete User
```
DELETE /api/chat/user?username=user_123
```
Delete a user from Agora Chat.

## Flutter Integration

### 1. Add Dependencies
```yaml
# pubspec.yaml
dependencies:
  http: ^1.1.0
  agora_chat_sdk: ^1.1.0
```

### 2. Chat Service Class
```dart
class ChatService {
  static const String baseUrl = 'https://your-api-url.com/api';
  
  // Get chat token
  static Future<Map<String, dynamic>> getChatToken(String userToken) async {
    final response = await http.get(
      Uri.parse('$baseUrl/chat/token'),
      headers: {'Authorization': 'Bearer $userToken'}
    );
    
    return jsonDecode(response.body);
  }
  
  // Initialize Agora Chat
  static Future<void> initializeChat(String userId, String token) async {
    await ChatClient.getInstance.loginWithAgoraToken(userId, token);
  }
}
```

### 3. Usage in Flutter
```dart
// Get chat token from Laravel API
final tokenResponse = await ChatService.getChatToken(userToken);

if (tokenResponse['success']) {
  // Initialize Agora Chat
  await ChatService.initializeChat(
    tokenResponse['agora_username'],
    tokenResponse['chat_token']
  );
  
  print('Chat initialized successfully');
}
```

## Testing

### 1. Test Connection
```bash
curl -X GET "http://localhost:8000/api/chat/test"
```

### 2. Test User Registration
```bash
curl -X POST "http://localhost:8000/api/chat/auto-register" \
  -H "Authorization: Bearer YOUR_USER_TOKEN"
```

### 3. Test Chat Token
```bash
curl -X GET "http://localhost:8000/api/chat/token" \
  -H "Authorization: Bearer YOUR_USER_TOKEN"
```

## Error Handling

### Common Issues:

1. **"Failed to get access token"**
   - Check if credentials are correct in .env file
   - Verify Agora Chat project is active
   - Check internet connection

2. **"User already exists"**
   - This is normal if user was previously registered
   - You can proceed with chat functionality

3. **"Invalid credentials"**
   - Double-check Client ID and Client Secret
   - Make sure Organization Name and App Name are correct

## Security Notes

1. **Never commit .env file to git**
2. **Keep Client Secret secure**
3. **Use HTTPS in production**
4. **Implement proper authentication**

## Support

For Agora Chat documentation:
- https://docs.agora.io/en/chat/develop/overview?platform=flutter
- https://console.agora.io

For Laravel HTTP Client:
- https://laravel.com/docs/11.x/http-client 