<?php

class Blog extends Module {

    /**
     * TODO
     */
    public static function getLastPost() {
        return Blog::post()->orderBy('created_at', 'DESC')->first();
    }

    /** 
     * TODO
     */
    public static function getPosts($limit = 3) {
        return Blog::post()->orderBy('created_at', 'DESC')->limit($limit)->get();
    }

    /**
     * TODO
     */
    public static function getCategories() {
        return Blog::category()->orderBy('name')->all();
    }

}
