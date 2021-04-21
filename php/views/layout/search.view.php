<div class="search-container">
    <ul class="search-type">
        <li id="hashtag-filter" <?=($content_type==='hashtag'||$content_type==="")?'class="active"':''?>><i class="fas fa-hashtag"></i> By <span>Hashtag</span></li>
        <li id="profile-filter" <?=$content_type==='profile'?'class="active"':''?>><i class="fas fa-id-badge"></i> By <span>Profile</span></li>
        <li id="video-filter" <?=$content_type==='video'?'class="active"':''?>><i class="fas fa-video"></i> By <span>Video Link</span></li>
    </ul>
    <form action="<?=URL?>/admin/" method="POST" class="search-form">
        <label for="<?=content_type_to_name($content_type)?>"><i class="fas fa-<?=content_type_to_fa($content_type)?>"></i></label>
        <input type="text" name="<?=content_type_to_name($content_type)?>" id="<?=content_type_to_name($content_type)?>" placeholder="Search <?=content_type_to_name($content_type)?>..." value="<?=(!empty($errors) && isset($form_post))?$form_post:''?>" required>
        <button type="submit"><i class="fas fa-arrow-right"></i></button>
    </form>
    <?php if (!empty($errors)): ?>
        <div class="field-error"><strong>Error<?=count($errors)>1?'s':''?>!</strong> <?php foreach ($errors as $error): ?><?=$error.'. '?><?php endforeach;?></div>
    <?php endif; ?>
</div>

<script>
    const add_active_nav = (to_id) => {
        $('#hashtag-filter').removeClass('active');
        $('#profile-filter').removeClass('active');
        $('#video-filter').removeClass('active');
        $('#'+to_id).addClass('active');
    }
    
    $('#hashtag-filter').on('click', (e) => {
        if ($('#hashtag-filter').hasClass('active')) { return };
        add_active_nav('hashtag-filter');
        $('.search-form').find('label').attr('for', 'hashtag');
        $('.search-form').find('label i').removeClass('fa-id-badge').removeClass('fa-video').addClass('fa-hashtag');
        $('.search-form').find('input').attr('name', 'hashtag').attr('id','hashtag').attr('placeholder', 'Search hashtag...');
    });
    $('#profile-filter').on('click', (e) => {
        if ($('#profile-filter').hasClass('active')) { return };
        add_active_nav('profile-filter');
        $('.search-form').find('label').attr('for', 'profile');
        $('.search-form').find('label i').removeClass('fa-hashtag').removeClass('fa-video').addClass('fa-id-badge');
        $('.search-form').find('input').attr('name', 'profile').attr('id','profile').attr('placeholder', 'Search profile...');
    });
    $('#video-filter').on('click', (e) => {
        if ($('#video-filter').hasClass('active')) { return };
        add_active_nav('video-filter');
        $('.search-form').find('label').attr('for', 'video');
        $('.search-form').find('label i').removeClass('fa-hashtag').removeClass('fa-id-badge').addClass('fa-video');
        $('.search-form').find('input').attr('name', 'video').attr('id','video').attr('placeholder', 'Search video...');
    });
</script>