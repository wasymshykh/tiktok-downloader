<div class="search-container">
    <form action="<?=URL?>/admin" method="post" class="search-form">
        <label for="hashtag">#</label>
        <input type="text" name="hashtag" id="hashtag" placeholder="Search hashtag..." required>
        <button type="submit"><i class="fas fa-arrow-right"></i></button>
    </form>
    
    <?php if (!empty($errors)): ?>
        <div class="field-error"><strong>Error<?=count($errors)>1?'s':''?>!</strong> <?php foreach ($errors as $error): ?><?=$error.'. '?><?php endforeach;?></div>
    <?php endif; ?>

</div>

<div class="results">
    <div class="results-title">
        <h3><strong>Saved</strong> Videos</h3>

        <div class="go-back">
            <a href="<?=URL?>/admin"><i class="fas fa-arrow-left"></i> Go Back</a>
        </div>
    </div>

    <?php if (!empty($videos)): ?>
    <div class="videos-container">
        <?php foreach ($videos as $video): ?>
        <div class="video-box" data-videoid="<?=$video['video_id']?>">
            <div class="video-box-dynamic">
                <div class="profile-top">
                    <div class="profile-icon">
                        <img src="<?=URL?>/saved/thumb/<?=$video['video_author_picture']?>">
                    </div>
                    <div class="profile-text">
                        <h3><?=$video['video_author_nick']?> <span><?=$video['video_author_username']?></span></h3>
                    </div>
                </div>
                <div class="cover">
                    <img src="<?=URL?>/saved/dynamic/<?=$video['video_cover']?>">
                </div>
                <div class="video-text">
                    <p><?=$video['video_desc']?></p>
                    <p class="date"><?=normal_date($video['video_dated'], 'M d, Y')?></p>
                </div>
            </div>
            <div class="video-box-actions">
                <div class="video-box-actions-top">
                    <div class="video-box-info">
                        <div class="v-b-i-icon"><i class="fas fa-check"></i> status</div>
                        <div class="v-b-i-text">SAVED</div>
                    </div>
                    <div class="video-box-info">
                        <div class="v-b-i-icon"><i class="fas fa-plus"></i> added</div>
                        <div class="v-b-i-text"><?=normal_date($video['video_created'], 'M d, Y')?></div>
                    </div>
                </div>
                <div class="video-box-actions-bottom">
                    <div class="video-box-action action-remove">
                        <div class="v-b-a-icon"><i class="fas fa-times"></i></div>
                        <div class="v-b-a-text">remove</div>
                        <div class="v-b-a-loading"><i class="fas fa-spinner fa-spin"></i></div>
                    </div>
                    <a href="<?=URL?>/saved/videos/<?=$video['video_id']?>.mp4" class="video-box-action action-download">
                        <div class="v-b-a-icon"><i class="fa fa-download"></i></div>
                        <div class="v-b-a-text">download</div>
                        <div class="v-b-a-loading"><i class="fas fa-spinner fa-spin"></i></div>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
        <div class="empty-error">No saved videos found. <a href="<?=URL?>/admin">Go back</a></div>
    <?php endif; ?>

</div>

<script>
    $('.action-remove').on('click', (e) => {
        let target = $(e.target.parentElement.parentElement.parentElement);
        const data = { remove_video: "", video_id: target.attr('data-videoid')};

        let t = $(e.target);
        t.addClass('loading');
        let ic = t.find('.v-b-a-icon i');

        $.ajax('<?=URL?>/admin/save_video.php', {
            method: "POST",
            data: data,
            success: (d) => {
                target.remove();
            },
            error: (e) => {
                if (e.responseJSON === undefined || e.responseJSON.type === 'error') {
                    t.removeClass('loading').addClass('error');
                    t.find('.v-b-a-text').text('error, retry!');
                    ic.removeClass('fa-times').addClass('fa-redo');
                }
            }
        });
    });
</script>

