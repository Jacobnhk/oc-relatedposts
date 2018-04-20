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
            'postSlug' => [
                'title'       => 'rainlab.blog::lang.settings.post_slug',
                'description' => 'rainlab.blog::lang.settings.post_slug_description',
                'default'     => '{{ :slug }}',
                'type'        => 'string'
            ]         
        ];
    }
    
    protected function prepareVars()
    {
        $this->postSlug = $this->page['postSlug'] = $this->property('postSlug');
    }    
    
    public function onRun()
    {
        $this->prepareVars();
        $this->relatedPosts = $this->page['relatedPosts'] = $this->loadRelatedPosts();     
    }    
    
    /**
     * Загрузка постов из категорий данной записи
     * $this->postPage и $this->categoryPage создаются компонентами самого RainLab.Blog
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
