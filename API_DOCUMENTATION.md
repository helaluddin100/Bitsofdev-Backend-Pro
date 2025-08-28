# API Documentation

This document describes the API endpoints available for your Next.js frontend application.

## Base URL
```
http://your-domain.com/api
```

## Authentication
Most endpoints are public and don't require authentication. Protected routes will be clearly marked.

## Blog API Endpoints

### Get All Blogs
```
GET /api/blogs
```

**Query Parameters:**
- `page` - Page number for pagination
- `per_page` - Items per page (default: 9)
- `category` - Filter by category slug
- `search` - Search in title, excerpt, and content
- `featured` - Get only featured posts (true/false)

**Example:**
```
GET /api/blogs?page=1&per_page=6&featured=true
```

### Get Featured Blogs
```
GET /api/blogs/featured
```

### Get Blog Categories
```
GET /api/blogs/categories
```

### Get Single Blog
```
GET /api/blogs/{slug}
```

## Project API Endpoints

### Get All Projects
```
GET /api/projects
```

**Query Parameters:**
- `page` - Page number for pagination
- `per_page` - Items per page (default: 9)
- `status` - Filter by status (planning, in_progress, completed, on_hold)
- `featured` - Get only featured projects (true/false)
- `search` - Search in title, description, and content
- `technologies` - Filter by technologies (comma-separated)

**Example:**
```
GET /api/projects?status=completed&featured=true&technologies=React,Laravel
```

### Get Featured Projects
```
GET /api/projects/featured
```

### Get Projects by Status
```
GET /api/projects/status/{status}
```

### Get Available Technologies
```
GET /api/projects/technologies
```

### Get Single Project
```
GET /api/projects/{slug}
```

## Team API Endpoints

### Get All Team Members
```
GET /api/team
```

### Get Featured Team Members
```
GET /api/team/featured
```

### Get Single Team Member
```
GET /api/team/{id}
```

## Pricing API Endpoints

### Get All Pricing Plans
```
GET /api/pricing
```

### Get Popular Pricing Plans
```
GET /api/pricing/popular
```

### Get Pricing Plans by Billing Cycle
```
GET /api/pricing/cycle/{cycle}
```
**Available cycles:** monthly, yearly, one-time

### Get Single Pricing Plan
```
GET /api/pricing/{slug}
```

## Response Format

All API responses follow this format:

```json
{
  "success": true,
  "data": {
    // Response data here
  }
}
```

### Error Response Format

```json
{
  "success": false,
  "message": "Error message here"
}
```

## Example Usage in Next.js

```javascript
// Fetch blogs
const fetchBlogs = async () => {
  try {
    const response = await fetch('/api/blogs?featured=true');
    const result = await response.json();
    
    if (result.success) {
      return result.data;
    }
  } catch (error) {
    console.error('Error fetching blogs:', error);
  }
};

// Fetch single blog
const fetchBlog = async (slug) => {
  try {
    const response = await fetch(`/api/blogs/${slug}`);
    const result = await response.json();
    
    if (result.success) {
      return result.data;
    }
  } catch (error) {
    console.error('Error fetching blog:', error);
  }
};
```

## Notes

- All endpoints return JSON responses
- Pagination is included where applicable
- Image URLs are relative paths that need to be prefixed with your domain
- The API automatically handles relationships (e.g., blog posts include category and user information)
- View counts are automatically incremented when viewing blog posts
