<?php

class Blog_Admin_CategoryController extends Controller {


    public static function manage()
    {
        $rows = array();
        $headers = array(
            array(
                'Name',
                'attributes' => array(
                    'colspan' => 2
                )
            )
        );

        $cats = Blog::category()->orderBy('name')->all();

        if($cats)
        {
            foreach($cats as $c)
            {
                $rows[] = array(
                    Html::a()->get($c->name, 'admin/blog/categories/edit/' . $c->id),
                    array(
                        Html::a()->get('Delete', 'admin/blog/categories/delete/' . $c->id),
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
                    '<em>No categories.</em>',
                    'attributes' => array(
                        'colspan' => 2
                    )
                )
            );
        }

        return array(
            'title' => 'Manage Categories',
            'content' => Html::table()->build($headers, $rows)
        );
    }


    public static function create()
    {
        if($_POST)
        {
            if(Html::form()->validate())
            {
                if(!Blog::category()->where('name', 'LIKE', $_POST['name'])->first())
                {
                    $categoryId = Blog::category()->insert(array(
                        'name' => $_POST['name'],
                        'slug' => String::slugify($_POST['name'])
                    ));

                    if($categoryId)
                    {
                        $_POST = array(); // Clear form
                        Message::ok('Category created successfully.');
                    }
                    else
                        Message::error('Error creating category. Please try again.');
                }
                else
                    Message::error('A category with that name already exists.');
            }
        }

        $formData[] = array(
            'fields' => array(
                'name' => array(
                    'title' => 'Name',
                    'type' => 'text',
                    'validate' => array('required')
                ),
                'submit' => array(
                    'type' => 'submit',
                    'value' => 'Create Category'
                )
            )
        );

        return array(
            'title' => 'Create Category',
            'content' => Html::form()->build($formData)
        );
    }


    public static function edit($id)
    {
        if(!$cat = Blog::category()->find($id))
            return ERROR_NOTFOUND;

        if($_POST)
        {
            if(Html::form()->validate())
            {
                $status = Blog::category()->where('id', '=', $id)->update(array(
                    'name' => $_POST['name'],
                    'slug' => $_POST['slug']
                ));

                if($status)
                {
                    $cat = Blog::category()->find($id);
                    Message::ok('Category updated successfully.');
                }
                else
                    Message::error('Error updating category, please try again.');
            }
        }

        $formData[] = array(
            'fields' => array(
                'name' => array(
                    'title' => 'Name',
                    'type' => 'text',
                    'validate' => array('required'),
                    'default_value' => $cat->name
                ),
                'slug' => array(
                    'title' => 'Slug',
                    'type' => 'text',
                    'validate' => array('required'),
                    'default_value' => $cat->slug
                ),
                'submit' => array(
                    'type' => 'submit',
                    'value' => 'Update Category'
                )
            )
        );

        return array(
            'title' => 'Edit Category',
            'content' => Html::form()->build($formData)
        );
    }


    public static function delete($id)
    {
        if(Blog::category()->delete($id))
            Message::ok('Category deleted successfully.');
        else
            Message::error('Error deleting category. Please try again.');
        
        Url::redirect('admin/blog/categories');
    }


}
