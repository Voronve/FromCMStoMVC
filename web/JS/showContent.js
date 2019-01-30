$(function(){
    
    console.log('Привет, это страый js ))');
    init_get();
    init_post();
    init_get2();
    init_post2();
});

function init_get() 
{
    $('a.ajaxArticleBodyByGet').one('click', function(){
        var contentId = $(this).attr('data-contentId');
        console.log('ID статьи = ', contentId); 
        showLoaderIdentity();
        $.ajax({
            method: "GET",
            url:'/ajax/showContentsHandler.php?articleId=' + contentId, 
            dataType: 'json'
        })
        .done (function(obj){
            hideLoaderIdentity();
            console.log('Ответ получен ' + obj.content);
            $('li.' + contentId).append(obj.content);
        })
        .fail(function(xhr, status, error){
            hideLoaderIdentity();
    
            console.log('ajaxError xhr:', xhr); // выводим значения переменных
            console.log('ajaxError status:', status);
            console.log('ajaxError error:', error);
    
            console.log('Ошибка соединения при получении данных (GET)');
        });
        
        return false;
        
    });  
}

function init_post() 
{
    $('a.ajaxArticleBodyByPost').one('click', function(){
        var content = $(this).attr('data-contentId');
        showLoaderIdentity();
        $.ajax({
            url:'/ajax/showContentsHandler.php', 
            dataType: 'json',
//            converters: 'json text',
            method: 'POST',
            data: {articleId : content }
        })
        .done (function(obj){
            hideLoaderIdentity();
            console.log('Ответ получен', obj.content);
            $('li.' + content).append(obj.content);
        })
        .fail(function(xhr, status, error){
            hideLoaderIdentity();
    
    
            console.log('Ошибка соединения с сервером (POST)');
            console.log('ajaxError xhr:', xhr); // выводим значения переменных
            console.log('ajaxError status:', status);
            console.log('ajaxError error:', error);
        });
        return false;
    });  
}

function init_get2(){
    $('a.ajaxArticleBodyByGet2').one('click', function(){
        var content = $(this).attr("data-contentId");
        showLoaderIdentity();
        $.ajax({
            method: "GET",
            url: "/ajax/showContentsHandler.php?articleId=" + content,
            dataType: "json",
            success: function(data){
                hideLoaderIdentity();
               $('li.' + content).append(data.content);
            },
            error: function(xhr, status, error){
                hideLoaderIdentity();
                console.log('ajaxError xhr: ' + xhr + ', ' + 'ajaxError status: ' + status +
                        ', ' + 'ajaxError : ' + error); 
            }
        });
        return false;
    });
}

function init_post2(){
    $('a.ajaxArticleBodyByPost2').one('click', function(){
        var content = $(this).data('contentId');
        showLoaderIdentity();
        $.ajax({
            method: "POST",
            url: "/ajax/showContentsHandler.php",
            dataType: "json",
            data: { articleId : content}, 
            success: function(data){
                hideLoaderIdentity();
                $('li.' + content).append(data.content);
            },
            error: function(xhr, status, error){
                hideLoaderIdentity();
                console.log('ajaxError xhr: ' + xhr + ', ' + 'ajaxError status: ' + status +
                        ', ' + 'ajaxError : ' + error); 
            }
        });
        return false;
    });
}


