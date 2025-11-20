# 🤖 Gemini API Setup Guide

## Step 1: Get Gemini API Key

1. **Go to:** https://makersuite.google.com/app/apikey
2. **Login** with your Google account
3. **Click** "Get API key"
4. **Select** "Create API key"
5. **Copy** the API key (it will look like: `AIzaSyC...`)

## Step 2: Test Your API Key

1. **Edit** `test_gemini_direct.php`
2. **Replace** `YOUR_API_KEY_HERE` with your actual API key
3. **Run:** `php test_gemini_direct.php`

## Step 3: Configure Laravel

Create or edit your `.env` file:
```env
AI_PROVIDER=gemini
AI_API_KEY=your-actual-api-key-here
```

## Step 4: Test Integration

Run the integration test:
```bash
php test_ai_integration.php
```

## Step 5: Test in Browser

1. **Start servers:**
   ```bash
   # Backend
   php artisan serve --host=127.0.0.1 --port=8000
   
   # Frontend
   cd ../sparkedev-Frontend
   npm run dev
   ```

2. **Open:** http://localhost:3000
3. **Click** chatbot icon
4. **Ask:** "What is machine learning?" or "Tell me a joke"

## Expected Results

- **Database questions:** Quick answers
- **Website-related questions:** Intelligent answers
- **Any other questions:** AI-generated answers from Gemini

## Troubleshooting

### ❌ "API key not set"
- Check your `.env` file
- Make sure `AI_API_KEY=your-key-here`

### ❌ "404 Not Found"
- Restart Laravel server
- Check if server is running on port 8000

### ❌ "API error"
- Verify your API key is correct
- Check if you have internet connection

## Benefits of Gemini API

✅ **Completely Free** - No cost for testing
✅ **Generous Limits** - 60 requests per minute
✅ **High Quality** - Google's advanced AI
✅ **Easy Setup** - Just need Google account

## Next Steps

1. Get your API key
2. Test with `test_gemini_direct.php`
3. Configure in `.env` file
4. Test integration
5. Enjoy your AI-powered chatbot! 🚀
