# Google OAuth Setup Guide for Panda Digital V3

## Prerequisites
- Google Cloud Console account
- Domain verification (for production)

## Step 1: Create Google Cloud Project

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing one
3. Enable the Google+ API and Google OAuth2 API

## Step 2: Configure OAuth Consent Screen

1. Go to "APIs & Services" > "OAuth consent screen"
2. Choose "External" user type
3. Fill in required information:
   - App name: "Panda Digital"
   - User support email: your email
   - Developer contact information: your email
4. Add scopes:
   - `.../auth/userinfo.email`
   - `.../auth/userinfo.profile`
5. Add test users (your email addresses)

## Step 3: Create OAuth 2.0 Credentials

1. Go to "APIs & Services" > "Credentials"
2. Click "Create Credentials" > "OAuth 2.0 Client IDs"
3. Choose "Web application"
4. Set authorized redirect URIs:
   - `https://v3.pandadigital.co.tz/auth/google-callback`
   - `http://localhost/pandadigitalV3/auth/google-callback` (for local development)
5. Copy the Client ID and Client Secret

## Step 4: Update Environment Configuration

1. Copy `env.example` to `.env`
2. Update the Google OAuth settings:

```env
# Google OAuth Configuration
GOOGLE_CLIENT_ID=your-google-client-id-here
GOOGLE_CLIENT_SECRET=your-google-client-secret-here
GOOGLE_REDIRECT_URI=https://v3.pandadigital.co.tz/auth/google-callback
```

## Step 5: Test the Integration

1. Go to the registration page: `/register.php`
2. You should see a "Jisajili na Google" button
3. Click it to test the OAuth flow
4. You should be redirected to Google for authentication
5. After successful authentication, you'll be redirected back and logged in

## Features

- **Automatic User Creation**: New Google users are automatically created with basic information
- **Existing User Login**: If a Google email already exists, the user is logged in
- **Random Password Generation**: Google users get secure random passwords
- **Role Assignment**: Google users default to 'user' role
- **Session Management**: Automatic login after successful OAuth

## Security Notes

- Google OAuth tokens are not stored in the database
- Users can still use regular email/password login
- Google ID is stored for future reference
- All OAuth communication uses HTTPS

## Troubleshooting

### Common Issues:

1. **"Google OAuth not configured" error**
   - Check that `.env` file has correct Google credentials
   - Verify Client ID and Client Secret are correct

2. **Redirect URI mismatch**
   - Ensure redirect URI in Google Console matches exactly
   - Check for trailing slashes or protocol differences

3. **API not enabled**
   - Make sure Google+ API and OAuth2 API are enabled
   - Check that the project is active

4. **Consent screen not configured**
   - Complete OAuth consent screen setup
   - Add test users if in testing mode

## Support

For issues with Google OAuth integration, check:
1. Google Cloud Console logs
2. Application error logs
3. Network tab in browser developer tools
4. Google OAuth documentation


