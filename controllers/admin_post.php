<?php

class Blog_Admin_PostController extends Controller {


    public static function manage()
    {
        $rows = array();
        $header = array(
            array(
                'Title', 
                'attributes' => array(
                    'colspan' => 2
                )
            )
        );
        
        $posts = Blog::post()->orderBy('created_at', 'DESC')->all();

        if($posts)
        {   
            foreach($posts as $p)
            {
                $rows[] = array(
                    Html::a()->get($p->title, 'admin/blog/posts/edit/' . $p->id),
                    array(
                        Html::a()->get('Delete', 'admin/blog/posts/delete/' . $p->id),
                        'attributes' => array(
                            'class' => 'right'
                        )
                    )
                );
            }
        }
        else
        {
            $rows[] = array(
                array(
                    '<em>No posts.</em>',
                    'attributes' => array(
                        'colspan' => 2
                    )
                )
            );
        }

        return array(
            'title' => 'Manage Posts',
            'content' => Html::table()->build($header, $rows)
        );
    }


    public static function create()
    {
        if($_POST)
        {
            if(Html::form()->validate())
            {
                $postId = Blog::post()->insert(array(
                    'user_id' => User::current()->id,
                    'title' => $_POST['title'],
                    'slug' => String::slugify($_POST['title']),
                    'body' => $_POST['body']
                ));

                if($postId)
                {
                    foreach($_POST['category_id'] as $catId)
                    {
                        Db::table('categories_posts')->insert(array(
                            'category_id' => $catId,
                            'post_id' => $postId
                        ));
                    }

                    Message::ok('Post created successfully.');
                    $_POST = array(); // Clear form
                }
                else
                    Message::error('Error creating post. Please try again.');
            }
        }

        $formData[] = array(
            'fields' => array(
                'title' => array(
                    'title' => 'Title',
                    'type' => 'text',
                    'validate' => array('required')
                ),
                'body' => array(
                    'title' => 'Body',
                    'type' => 'textarea'
                ),
                'category_id[]' => array(
                    'title' => 'Category',
                    'type' => 'select',
                    'options' => self::_getSelectCategories(),
                    'attributes' => array(
                        'multiple' => 'multiple'
                    ),
                    'validate' => array('required')
                ),
                'submit' => array(
                    'type' => 'submit',
                    'value' => 'Create Post'
                )
            )
        );

        return array(
            'title' => 'Create Post',
            'content' => Html::form()->build($formData)
        );
    }


    public static function edit($id)
    {
        if(!$post = Blog::post()->find($id))
            return ERROR_NOTFOUND;

        if($_POST)
        {
            if(Html::form()->validate())
            {
                $status = Blog::post()->where('id', '=', $id)->update(array(
                    'title' => $_POST['title'],
                    'slug' => $_POST['slug'],
                    'body' => $_POST['body']
                ));

                if($status)
                {
                    // Clear old categories
                    Db::table('categories_posts')->where('post_id', '=', $id)->delete();

                    // Insert new categories
                    foreach($_POST['category_id'] as $catId)
                    {
                        Db::table('categories_posts')->insert(array(
                            'category_id' => $catId,
                            'post_id' => $id
                        ));
                    }

                    Message::ok('Post updated successfully.');
                }
                else
                    Message::error('Error updating post. Please try again.');
            }
        }

        $formData[] = array(
            'fields' => array(
                'title' => array(
                    'title' => 'Title',
                    'type' => 'text',
                    'validate' => array('required'),
                    'default_value' => $post->title
                ),
                'slug' => array(
                    'title' => 'Slug',
                    'type' => 'text',
                    'validate' => array('required'),
                    'default_value' => $post->slug
                ),
                'body' => array(
                    'title' => 'Body',
                    'type' => 'textarea',
                    'default_value' => $post->title
                ),
                'category_id[]' => array(
                    'title' => 'Category',
                    'type' => 'select',
                    'options' => self::_getSelectCategories(),
                    'attributes' => array(
                        'multiple' => 'multiple'
                    ),
                    'validate' => array('required')
                ),
                'submit' => array(
                    'type' => 'submit',
                    'value' => 'Update Post'
                )
            )
        );

        return array(
            'title' => 'Edit Post',
            'content' => Html::form()->build($formData)
        );
    }


    public static function delete($id)
    {
        Db::table('categories_posts')->where('post_id', '=', $id)->delete();

        if(Blog::post()->delete($id))
            Message::ok('Post deleted successfully.');
        else
            Message::error('Error deleting post. Please try again.');

        Url::redirect('admin/blog/posts');
    }


    /**
     * Gets categories formatted for html select box.
     */
    private static function _getSelectCategories()
    {
        $categories = Blog::category()->orderBy('name')->all();
        $sortedCategories = array();

        foreach($categories as $c)
            $sortedCategories[$c->id] = $c->name;

        return $sortedCategories;
    }


}
