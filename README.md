# iwwt
[https://iwwt.fly.dev/](https://iwwt.fly.dev/)

Interview Task for Wise Team by Augustinas Kalvis.

## Notes
> Create a web application using the Symfony framework that allows library
> employees to register new books into the library’s system

I interpret this as registration is required, however, to forbid any random
users to register to the website I would normally add something like an 
invitation link.

I wanted to play around with picoCSS - a css framework that prides itself in 
avoiding classes as much as possible by levaraging semantic html. Figured it 
would be more than enough for a small CRUD app.

Book editing and deletion is accessed available the user is logged in. The 
frontend handles this by only rendering the edit/remove buttons when the user 
is logged in, while the backend flat out redirects the user to a login page if
they try to access pages they have no access to.

> Add pagination to the book list using JavaScript.

This point made me raise some questions. I assume that the purpose was to not
spend too much of the candidate's time, however, functionally it doesn't make
sense. What's the point in adding pagination when all of the information is 
already available in memory? Due to this confusion, I decided not to tackle
this point.

## Running the application locally

```
docker compose up -d database
symfony composer install
symfony server:start
```

## Deployment

I use fly.io for deployments. While flyctl does not directly support symfony,
it does support laravel. Fly thinks we have a Laravel application if it 
detects an artisan file, which is why it exists in root. Since the environments
in which symfony and laravel applications run are very similar, it will be good
enough.

We use a single docker image for nginx and php-fpm because it facilitates 
deployments with fly.io.

Fly.io will handle postgres deployments automatically and will insert the
correct DATABASE_URL env. You will need to set other envs manually via 
`fly secrets`.

The deployment with fly.io is quick and dirty. It relies on the user having a
proper environment, which means that it's easy to make a mistake either by 
adding files that shouldn't be in prod or deploying untested code. This would
normally be circumvented by having a proper ci/cd pipeline where code would be
tested and deployed only if tests go through and/or are approved.

After deploying I tested my application, after which I found that after a login
I'd be redirected back to the login screen. After one of the deployments I 
received a "Invalid CSRF token." error. I wasted 2 hours not realizing that I
was deploying from my laptop without first clearing the cache. In hindsight, I
should've spent some time to set up some CI/CD pipelines so that my deployments
would be cleaner, but oh well.
