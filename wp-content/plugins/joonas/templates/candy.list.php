<?php

?>

<div class="candyshop-v1">

    <button id="load-candies" class="button">Load Candies!</button>

    <div id="candy-list">
        <ul></ul>
    </div>

    <hr/>

    <form id="candy-create" method="POST">
        <input type="text" name="name" value="" placeholder="name..."/>
        <input type="text" name="details" value="" placeholder="details..."/>
        <input type="submit" class="button" value="Create Candy"/>
    </form>
</div>

<script type="text/javascript">
$(document).ready(function(){
    function update_candy_list(){
        var cont = $("#candy-list");
        cont.html('');

        $.get( "/wp-json/candyshop/v1/candy", function( data ) {
            var d = data || [];
            d.forEach(function(el, idx){
                cont.append('<div style="border: 1px solid #ccc; padding: 1em 2em; height: 5em; overflow-y: scroll; margin: .5em auto;">' + JSON.stringify(el) + '</div>');
            });
        });
    }

    function create_candy(evt){
        evt.preventDefault();

        var $this = $(this);
        var query = {
            name: $this.find('input[name=name]').val(),
            details: $this.find('input[name=details]').val()
        };
        var posting = $.post('/wp-json/candyshop/v1/candy', query);

        posting.done(function(data){
            alert('Done!');
            update_candy_list();
        });
        posting.fail(function(){
            alert('Something wrong');
        });

        return false;
    }

    $("#load-candies").click(update_candy_list);
    $("#candy-create").submit(create_candy);
    update_candy_list();
});
</script>
