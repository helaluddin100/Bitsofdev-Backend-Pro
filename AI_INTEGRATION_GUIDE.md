# 🤖 AI Integration Guide for Chatbot

## Overview
Your chatbot now supports external AI APIs (ChatGPT/Gemini) to answer any type of question, even if it's not in your database!

## How It Works

1. **Database Check** - First checks your existing Q&A pairs
2. **Intelligent Analysis** - If no match, analyzes website content for relevant answers
3. **AI API Fallback** - If still no answer, calls external AI API (ChatGPT/Gemini)
4. **Contact Suggestion** - Final fallback for complex queries

## Setup Instructions

### Option 1: OpenAI ChatGPT API (Recommended)

1. **Get API Key:**
   - Go to https://platform.openai.com/api-keys
   - Create a new API key
   - Copy the key (starts with `sk-`)

2. **Configure in .env file:**
   ```env
   AI_PROVIDER=openai
   AI_API_KEY=sk-your-api-key-here
   ```

3. **Pricing:** 
   - GPT-3.5-turbo: $0.0015 per 1K tokens (very cheap)
   - Free tier: $5 credit for new users

### Option 2: Google Gemini API (Free)

1. **Get API Key:**
   - Go to https://makersuite.google.com/app/apikey
   - Create a new API key
   - Copy the key

2. **Configure in .env file:**
   ```env
   AI_PROVIDER=gemini
   AI_API_KEY=your-gemini-api-key-here
   ```

3. **Pricing:**
   - Free tier: 60 requests per minute
   - Very generous free limits

### Option 3: Disable AI (Current State)

If you don't want to use AI APIs:
```env
AI_PROVIDER=none
AI_API_KEY=
```

## Testing

Run the test script to see AI integration in action:
```bash
php test_ai_integration.php
```

## Example Questions That Will Use AI

- "What is the weather like today?"
- "How do I cook pasta?"
- "What is machine learning?"
- "Tell me a joke"
- "What is the capital of Japan?"
- "How to learn programming?"
- "What is artificial intelligence?"
- "Explain quantum computing"
- "What are the benefits of exercise?"
- "How to start a business?"

## Response Types

- `quick_answer` - From your database
- `intelligent_answer` - From website content analysis
- `ai_generated` - From external AI API
- `contact_suggestion` - Fallback response

## Benefits

✅ **Any Question Answered** - No more "I don't know" responses
✅ **Professional Context** - AI knows about sparkedev
✅ **Cost Effective** - Very cheap API calls
✅ **Fallback System** - Multiple layers of response
✅ **No Backend Hanging** - Robust error handling

## Security Notes

- API keys are stored in environment variables
- No sensitive data sent to AI APIs
- Context is limited to company information
- Rate limiting prevents abuse

## Troubleshooting

1. **404 Errors:** Check if server is running
2. **No AI Responses:** Verify API key and provider
3. **Timeout Errors:** Check internet connection
4. **Rate Limits:** Wait a moment and try again

## Next Steps

1. Choose an AI provider (OpenAI recommended)
2. Get an API key
3. Add to .env file
4. Test with various questions
5. Enjoy your super-powered chatbot! 🚀
