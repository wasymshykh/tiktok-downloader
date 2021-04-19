<div class="nav"><a href="<?=URL?>/admin/saved.php" class="blue-button"><i class="fas fa-photo-video"></i> View Saved</a></div>

<div class="search-container">
    <form action="" method="post" class="search-form">
        <label for="hashtag">#</label>
        <input type="text" name="hashtag" id="hashtag" placeholder="Search hashtag..." required>
        <button type="submit"><i class="fas fa-arrow-right"></i></button>
    </form>
</div>

<?php if (!empty($hashtag)): ?>
<div class="results">
    <div class="results-title">
        <h3>Search result for <strong>#<?=$hashtag?></strong></h3>

        <div class="go-back">
            <a href="<?=URL?>/admin"><i class="fas fa-times"></i> Exit Search</a>
        </div>
    </div>

    <?php if (!empty($videos)): ?>
    <div class="videos-container">
        <?php foreach ($videos as $video): ?>
        <div class="video-box" 
            data-videoid="<?=$video->id?>" data-username="<?=$video->author->uniqueId?>"
            data-author="<?=$video->author->id?>" data-nick="<?=$video->author->nickname?>"
            data-thumb="<?=$video->author->avatarThumb?>" data-cover="<?=$video->video->dynamicCover?>"
            data-description="<?=$video->desc?>" data-created="<?=$video->createTime?>"
        >
            <div class="video-box-dynamic">
                <div class="profile-top">
                    <div class="profile-icon">
                        <img src="<?=$video->author->avatarThumb?>">
                    </div>
                    <div class="profile-text">
                        <h3><?=$video->author->nickname?> <span><?=$video->author->uniqueId?></span></h3>
                    </div>
                </div>
                <div class="cover">
                    <img src="<?=$video->video->dynamicCover?>">
                </div>
                <div class="video-text">
                    <p><?=$video->desc?></p>
                    <p class="date"><?=date('M d, Y', $video->createTime)?></p>
                </div>
            </div>
            <div class="video-box-actions">
                <div class="video-box-actions-top">
                    <div class="video-box-info">
                        <div class="v-b-i-icon"><i class="fas fa-heart"></i> hearts</div>
                        <div class="v-b-i-text"><?=number_format($video->stats->diggCount)?></div>
                    </div>
                    <div class="video-box-info">
                        <div class="v-b-i-icon"><i class="fas fa-eye"></i> views</div>
                        <div class="v-b-i-text"><?=number_format($video->stats->playCount)?></div>
                    </div>
                </div>
                <div class="video-box-actions-bottom">
                    <div class="video-box-action <?=array_key_exists($video->id, $saved_ids)? 'action-saved': 'action-save'?>">
                        <div class="v-b-a-icon"><i class="fas <?=array_key_exists($video->id, $saved_ids)? 'fa-check': 'fa-file-upload'?>"></i></div>
                        <div class="v-b-a-text"><?=array_key_exists($video->id, $saved_ids)? 'saved': 'save'?></div>
                        <div class="v-b-a-loading"><i class="fas fa-spinner fa-spin"></i></div>
                    </div>
                    <div class="video-box-action action-download">
                        <div class="v-b-a-icon"><i class="fa fa-download"></i></div>
                        <div class="v-b-a-text">download</div>
                        <div class="v-b-a-loading"><i class="fas fa-spinner fa-spin"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="page-pagination">
        <?php if ($page != 1):?>
        <a href="<?=URL?>/admin/?h=<?=$hashtag?>&p=<?=$page-1?>" class="btn-previous"><i class="fas fa-arrow-left"></i> Previous</a>
        <?php endif; ?>
        
        <?php if ($page != 10):?>
        <a href="<?=URL?>/admin/?h=<?=$hashtag?>&p=<?=$page+1?>" class="btn-next">Next <i class="fas fa-arrow-right"></i></a>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="empty-error">No videos found for this hashtag. <a href="<?=URL?>/admin">Go back</a></div>
    <?php endif; ?>

<?php endif; ?>

<script>
    $('.action-save').on('click', (e) => {
        let target = $(e.target.parentElement.parentElement.parentElement);
        const data = { save_video: "", video_id: target.attr('data-videoid'),
        username: target.attr('data-username'), author: target.attr('data-author'), thumb: target.attr('data-thumb'),
        nick: target.attr('data-nick'), cover: target.attr('data-cover'), desc: target.attr('data-description'), created: target.attr('data-created')};

        let t = $(e.target);
        t.addClass('loading');
        let ic = t.find('.v-b-a-icon i');

        $.ajax('<?=URL?>/admin/save_video.php', {
            method: "POST",
            data: data,
            success: (d) => {
                t.removeClass('loading').removeClass('error').addClass('saved');
                t.find('.v-b-a-text').text('saved');
                ic.removeClass('fa-file-upload');
                ic.addClass('fa-check');
            },
            error: (e) => {
                if (e.responseJSON === undefined || e.responseJSON.type === 'error') {
                    t.removeClass('loading').addClass('error');
                    t.find('.v-b-a-text').text('error, retry!');
                    ic.removeClass('fa-file-upload');
                    ic.addClass('fa-redo');
                }
            }
        });
    });
    $('.action-download').on('click', (e) => {
        let target = $(e.target.parentElement.parentElement.parentElement);
        const data = { download_video: "", video_id: target.attr('data-videoid'), username: target.attr('data-username')};

        let t = $(e.target);
        t.addClass('loading');
        let ic = t.find('.v-b-a-icon i');

        $.ajax('<?=URL?>/admin/save_video.php', {
            method: "POST",
            data: data,
            success: (d) => {
                t.removeClass('loading').removeClass('error').removeClass('download').addClass('ready');
                t.find('.v-b-a-text').text('ready');
                ic.removeClass('fa-download').addClass('fa-check');
                
                if (d.code === 200 && d.message !== "") {
                    download(d.message, data.video_id+".mp4");
                }
            },
            error: (e) => {
                if (e.responseJSON === undefined || e.responseJSON.type === 'error') {
                    t.removeClass('loading').addClass('error');
                    t.find('.v-b-a-text').text('error, retry!');
                    ic.removeClass('fa-download').addClass('fa-redo');
                }
            }
        });
    });

    function download(url, filename) {
        url = "data:video/mp4,"+url;
        fetch(url).then(function(t) {
            return t.blob().then((b)=>{
                var a = document.createElement("a");
                a.href = URL.createObjectURL(b);
                a.setAttribute("download", filename);
                a.click();
            });
        });
    }

</script>
