# Unicorn farm API

## Basics

This is a traditional Symfony 6.2 app, meaning after pulling it, you'll need to run `composer install`, and run the
migrations.

You need to create your own .env.local file. Please see the .env.dist file for an example.

To populate the database with dummy data, you can run `bin/console doctrine:fixtures:load -n`

## Routes

### all_posts

GET: /api/v1/posts/

### all_user_posts

GET: /api/v1/users/{userId}/posts/

### create_post_for_user

POST: /api/v1/posts/  
JSON payload:

```
{
	"user": int,
	"body": string,
	"unicorn": int
}
```

Unicorn is optional

### edit_user_post

PATCH: /api/v1/users/{userId}/posts/{postId}  
JSON payload:

```
{
	"body": string
}
```

### delete_user_post

DELETE: /api/v1/users/{userId}/posts/  
JSON payload:

```
{
	"post": int
}
```

### purchase_unicorn

POST: /api/v1/transaction/purchase/  
JSON payload:

```
{
	"user": int,
	"unicorn": int
}
```

### all_unicorns

GET: /api/v1/unicorns/  