<div>
    <div class="toolbar">
        <h1>$_POST</h1>
        <a href="#" class="button back">Back</a>
    </div>
    <pre style="margin:10px"><?php
    function htmlspecialchars_array($string)
    {
        if (is_array($string))
        {
            return array_map('htmlspecialchars_array', $string);
        }
        else
        {
            return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
        }
    }
    var_dump(htmlspecialchars_array($_POST));
    ?></pre>
</div>

