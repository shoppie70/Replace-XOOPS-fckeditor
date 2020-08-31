# Replace_fckeditor
Replacing from fckeditor to ckeditor with CDN in XOOPS.

## Usage

### Replace in XOOPS
1. Remove `common/fckeditor` folder and upload this folder.
2. Replace script in modules.

e.g. `xoops_trust_path/modules/d3forum/include/wysiwyg_editors.inc.php` line 12
```php
$d3forum_wysiwyg_header = <<<EOM
    <script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const filebrowserUploadUrl = '/fckeditor_xoops/mediaUpload.php?param=1';

        CKEDITOR.replace('{$d3forum_wysiwygs['name']}', {
            language: 'ja',
            toolbarCanCollapse: true,
            filebrowserUploadMethod: 'form',
            filebrowserUploadUrl: filebrowserUploadUrl,
            image_previewText: '画像アップロード',
            // customConfig: "/fckeditor_xoops/config.js
        });
    });
    </script>
EOM;
```

### Customize a toolbar
1. Loading `<script src="//cdn.ckeditor.com/4.14.1/full/ckeditor.js"></script>` insted of 
2. Uncomment `customConfig: "/fckeditor_xoops/config.js`.
