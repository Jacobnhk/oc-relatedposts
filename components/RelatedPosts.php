<?php namespace Bree7e\BlogRelatedPosts\Components;

use DB;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use RainLab\Blog\Models\Post;

class RelatedPosts extends ComponentBase
{
    /**
     * Slug - часть url. 
     * Понять на какой записи находится компонент
     * @var string
     */
    public $postSlug;

    /**
     * Reference to the page name for linking to posts.
     * @var string
     */
    public $postPage;

    /**
     * Reference to the page name for linking to categories.
     * @var string
     */
    public $categoryPage;    

    /**
     * A collection of posts to display
     * @var Collection|null
     */
    public $relatedPosts;

    

    public function componentDetails()
    {
        return [
            'name'        => 'relatedPosts',
            'description' => 'Список постов в категориях записи'
        ];
    }

    public function defineProperties()
    {
        return [
            'categoryPage' => [
                'title'       => 'rainlab.blog::lang.settings.posts_category',
                'description' => 'rainlab.blog::lang.settings.posts_category_description',
                'type'        => 'dropdown',
                'default'     => 'blog/category',
            ],
            'postPage' => [
                'title'       => 'rainlab.blog::lang.settings.posts_post',
                'description' => 'rainlab.blog::lang.settings.posts_post_description',
                'type'        => 'dropdown',
                'default'     => 'blog/post',
            ],            
            'postSlug' => [
                'title'       => 'rainlab.blog::lang.settings.post_slug',
                'description' => 'rainlab.blog::lang.settings.post_slug_description',
                'default'     => '{{ :slug }}',
                'type'        => 'string'
            ]         
        ];
    }

    public function getCategoryPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getPostPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }    
    
    protected function prepareVars()
    {
        $this->postSlug = $this->page['postSlug'] = $this->property('postSlug');
         // Page links
        $this->postPage = $this->page['postPage'] = $this->property('postPage');
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');        
    }    
    
    public function onRun()
    {
        $this->prepareVars();
        $this->relatedPosts = $this->page['relatedPosts'] = $this->loadRelatedPosts();     
    }    
    
    /**
     * Загрузка постов из категорий данной записи
     * 
     * @return Collection
     */
    protected function loadRelatedPosts()
    {
        $post = Post::whereSlug($this->page['slug'])->first();
        if (!$post || !$post->categories->count()) {
            return null;
        }
        
        $categorieIds = $post->categories->pluck('id');

        $query = Post::isPublished()
            ->where('id', '<>', $post->id)
            ->filterCategories($categorieIds)
            ->with('categories');

        $posts = $query->get()->each(function($post) {
            $post->setUrl($this->postPage, $this->controller);

            $post->categories->each(function($category) {
                $category->setUrl($this->categoryPage, $this->controller);
            });
        });

        // {% set displayDate = archive.year ~ '-' ~ archive.month ~ '-01' %}   
        // foreach ($posts as $post) {
        //     $post->displayDate = Argon::createFromDate($post->year, $post->month);
        // }

        return $posts;
    } 
    
}
