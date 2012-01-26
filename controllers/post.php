<?php

class Blog_PostController extends Controller {

        
    public static function all($page = 0) {
        View::data('posts', Blog::post()->orderBy('created_at', 'DESC')->all());
    }

    
    public static function single($postSlug)
    {
        if(!$post = Blog::post()->find($postSlug))
            return ERROR_NOTFOUND;

        View::data('post', $post);
    }


    public static function postsByCategory($categorySlug)
    {
        $posts = Blog::post()
            ->select('blog_posts.*')
            ->leftJoin('categories_posts', 'categories_posts.post_id', '=', 'blog_posts.id')
            ->leftJoin('blog_categories', 'blog_categories.id', '=', 'categories_posts.category_id')
            ->where('blog_categories.slug', '=', $categorySlug)
            ->orderBy('blog_posts.created_at', 'DESC')
            ->all();

        View::data('posts', $posts);
    }


    public static function search()
    {

    }


}
