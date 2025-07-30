# Agora Token Generation Setup Guide

## Overview
This setup provides comprehensive Agora token generation capabilities including:
- **RTC Tokens** - For video/voice calling
- **RTM Tokens** - For real-time messaging
- **App Access Tokens** - For secure application access
- **Chat Tokens** - For Agora Chat service

## Prerequisites

### 1. Agora Console Setup
1. Go to [Agora Console](https://console.agora.io/)
2. Create a new project or use existing one
3. Get the following credentials:
   - **App ID**
   - **App Certificate**
   - **Organization Name** (for Chat)
   - **App Name** (for Chat)
   - **Client ID** (for Chat)
   - **Client Secret** (for Chat)

### 2. Environment Configuration
Add these variables to your `.env` file:

```env
# Agora Video Calling & RTM Configuration
AGORA_APP_ID=your_app_id_here
AGORA_APP_CERTIFICATE=your_app_certificate_here

# Agora Chat Configuration (existing)
AGORA_CHAT_ORG_NAME=your_org_name
AGORA_CHAT_APP_NAME=your_app_name
AGORA_CHAT_CLIENT_ID=your_client_id
AGORA_CHAT_CLIENT_SECRET=your_client_secret
AGORA_CHAT_BASE_URL=https://a41.easemob.com
```

## API Endpoints

### 1. RTC Token Generation
**POST** `/api/agora/rtc-token`

Generate token for video/voice calling.

**Request Body:**
```json
{
    "channel_name": "test_channel",
    "uid": "user123",
    "role": 1,
    "expire_in": 3600
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "token": "generated_rtc_token",
        "channel_name": "test_channel",
        "uid": "user123",
        "role": 1,
        "expires_in": 3600
    }
}
```

### 2. RTM Token Generation
**POST** `/api/agora/rtm-token`

Generate token for real-time messaging.

**Request Body:**
```json
{
    "user_id": "user123",
    "role": 1,
    "expire_in": 3600
}
```

### 3. App Access Token
**POST** `/api/agora/app-access-token`

Generate secure app access token.

**Request Body:**
```json
{
    "user_id": "user123",
    "permissions": ["read", "write"],
    "expire_in": 86400
}
```

### 4. All Tokens
**GET** `/api/agora/all-tokens?channel_name=test_channel`

Get all tokens for authenticated user.

### 5. Test Services
**GET** `/api/agora/test`

Test all Agora services and configurations.

### 6. Validate Token
**POST** `/api/agora/validate-token`

Validate a generated token.

**Request Body:**
```json
{
    "token": "your_token_here",
    "type": "rtc"
}
```

### 7. Config Status
**GET** `/api/agora/config-status`

Check configuration status.

## Testing

### 1. Test RTC Token Generation
```bash
curl -X POST http://127.0.0.1:8000/api/agora/rtc-token \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_USER_TOKEN" \
  -d '{
    "channel_name": "test_channel",
    "uid": "user123",
    "role": 1,
    "expire_in": 3600
  }'
```

### 2. Test RTM Token Generation
```bash
curl -X POST http://127.0.0.1:8000/api/agora/rtm-token \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_USER_TOKEN" \
  -d '{
    "user_id": "user123",
    "role": 1,
    "expire_in": 3600
  }'
```

### 3. Test All Services
```bash
curl -X GET http://127.0.0.1:8000/api/agora/test \
  -H "Authorization: Bearer YOUR_USER_TOKEN"
```

### 4. Check Configuration
```bash
curl -X GET http://127.0.0.1:8000/api/agora/config-status \
  -H "Authorization: Bearer YOUR_USER_TOKEN"
```

## Token Types & Roles

### RTC Token Roles
- `0` - Attendee (can only receive streams)
- `1` - Publisher (can publish and receive streams)
- `2` - Subscriber (can only receive streams)
- `101` - Admin (full privileges)

### RTM Token Roles
- `1` - RTM User (can send/receive messages)

### Token Expiration
- **Minimum**: 60 seconds
- **Maximum**: 86400 seconds (24 hours)
- **Default**: 3600 seconds (1 hour)

## Error Handling

### Common Errors
1. **Missing Credentials**: Check if all Agora credentials are set in `.env`
2. **Invalid Channel Name**: Channel name must be 1-64 characters
3. **Invalid UID**: UID must be 1-32 characters
4. **Token Generation Failed**: Check App ID and Certificate

### Error Response Format
```json
{
    "success": false,
    "message": "Error description"
}
```

## Security Notes

1. **Never expose App Certificate** in client-side code
2. **Use HTTPS** in production
3. **Validate user permissions** before generating tokens
4. **Set appropriate expiration times** for tokens
5. **Log token generation** for audit purposes

## Flutter Integration

### Example Flutter Code
```dart
class AgoraService {
  static Future<Map<String, dynamic>> getRtcToken(String channelName, String uid) async {
    final response = await http.post(
      Uri.parse('http://127.0.0.1:8000/api/agora/rtc-token'),
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer $userToken',
      },
      body: jsonEncode({
        'channel_name': channelName,
        'uid': uid,
        'role': 1,
        'expire_in': 3600,
      }),
    );
    
    return jsonDecode(response.body);
  }
}
```

## Troubleshooting

### 1. "Failed to generate token"
- Check if App ID and Certificate are correct
- Verify credentials in Agora Console
- Check Laravel logs for detailed error

### 2. "Invalid credentials"
- Clear Laravel cache: `php artisan config:clear`
- Restart Laravel server
- Verify `.env` file format

### 3. "Token validation failed"
- Check token format
- Verify token hasn't expired
- Ensure correct token type

## Files Created/Modified

### New Files
- `app/Services/Agora/RtcTokenBuilder.php`
- `app/Services/Agora/RtmTokenBuilder.php`
- `app/Services/AgoraService.php`
- `app/Http/Controllers/Api/AgoraController.php`
- `config/agora.php`

### Modified Files
- `routes/api.php` - Added Agora token routes
- `.env` - Added Agora credentials

## Next Steps

1. **Configure Agora Console** with your credentials
2. **Test token generation** using provided endpoints
3. **Integrate with Flutter** app
4. **Implement video calling** using generated tokens
5. **Add real-time messaging** using RTM tokens 