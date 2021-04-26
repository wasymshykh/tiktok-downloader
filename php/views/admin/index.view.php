<div class="nav"><a href="<?=URL?>/admin/saved.php" class="blue-button"><i class="fas fa-photo-video"></i> View Saved</a></div>

<?php include_once DIR.'views/layout/search.view.php'; ?>

<?php if (!empty($content_type)): ?>
<div class="results">
    <div class="results-title">
        <h3>Search result for <strong><?=($content_type === 'hashtag')?"#$hashtag":($content_type === 'video'?"/@$profile_name/video/$video_id": ($content_type === 'profile' ? "@$profile_name": ''))?></strong></h3>

        <div class="go-back">
            <a href="<?=URL?>/admin/"><i class="fas fa-times"></i> Exit Search</a>
        </div>
    </div>

    <?php if (!empty($videos)): ?>
        <div class="videos-container">
            <?php foreach ($videos as $video): ?>
            <div class="video-box" data-videoid="<?=$video->id?>" data-username="<?=$video->author->uniqueId?>"
                data-author="<?=$video->author->id?>" data-nick="<?=$video->author->nickname?>"
                data-thumb="<?=$video->author->avatarThumb?>" data-cover="<?=$video->video->dynamicCover?>"
                data-description="<?=$video->desc?>" data-created="<?=$video->createTime?>">
                <div class="video-box-dynamic">
                    <div class="profile-top">
                        <a href="https://www.tiktok.com/@<?=$video->author->uniqueId?>" target="_blank" class="profile-icon">
                            <img src="<?=$video->author->avatarThumb?>">
                        </a>
                        <div class="profile-text">
                            <h3><?=$video->author->nickname?> <span><?=$video->author->uniqueId?></span></h3>
                        </div>
                    </div>
                    <a href="https://www.tiktok.com/@<?=$video->author->uniqueId?>/video/<?=($video->id)?>" target="_blank" class="cover">
                        <img src="<?=$video->video->dynamicCover?>">
                    </a>
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
                    <div class="video-box-events">
                        <div class="video-box-events-box" data-clipboard-text="https://www.tiktok.com/@<?=$video->author->uniqueId?>">
                            <div class="v-b-e-icon"><i class="fa fa-copy"></i></div>
                            <div class="v-b-e-text-sub">copy</div>
                            <div class="v-b-e-text">Profile Link</div>
                        </div>
                        <div class="video-box-events-box" data-clipboard-text="https://www.tiktok.com/@<?=$video->author->uniqueId?>/video/<?=($video->id)?>">
                            <div class="v-b-e-icon"><i class="fa fa-copy"></i></div>
                            <div class="v-b-e-text-sub">copy</div>
                            <div class="v-b-e-text">Video Link</div>
                        </div>
                    </div>
                    <div class="video-box-actions-bottom">
                        <div class="video-box-action <?=array_key_exists($video->id, $saved_ids)? 'action-saved': 'action-save'?>">
                            <div class="v-b-a-icon"><i class="fas <?=array_key_exists($video->id, $saved_ids)? 'fa-check': 'fa-file-upload'?>"></i></div>
                            <div class="v-b-a-text"><?=array_key_exists($video->id, $saved_ids)? 'saved': 'save'?></div>
                            <div class="v-b-a-loading"><i class="fas fa-spinner fa-spin"></i></div>
                        </div>
                        <a <?=array_key_exists($video->id, $saved_ids)? 'href="'.URL.'/saved/videos/'.$saved_ids[$video->id]['video_index'].'.mp4"': ''?> class="video-box-action <?=array_key_exists($video->id, $saved_ids)? 'no-request': 'action-download'?>">
                            <div class="v-b-a-icon"><i class="fa fa-download"></i></div>
                            <div class="v-b-a-text">download</div>
                            <div class="v-b-a-loading"><i class="fas fa-spinner fa-spin"></i></div>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ($content_type === 'hashtag'): ?>
        <div class="page-pagination">
            <?php if ($page != 1):?>
            <a href="<?=URL?>/admin/?<?=$content_type==='hashtag'?"h=$hashtag":"u=$profile_name"?>&p=<?=$page-1?>" class="btn-previous"><i class="fas fa-arrow-left"></i> Previous</a>
            <?php endif; ?>
            
            <?php if ($page != 10):?>
            <a href="<?=URL?>/admin/?<?=$content_type==='hashtag'?"h=$hashtag":"u=$profile_name"?>&p=<?=$page+1?>" class="btn-next">Next <i class="fas fa-arrow-right"></i></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="empty-error">No videos found for this <?=$content_type === 'hashtag' ? 'hashtag' : ($content_type === 'video' ? 'link' : ($content_type === 'profile' ? 'profile' : ''))?>. <a href="<?=URL?>/admin">Go back</a></div>
    <?php endif; ?>

<?php endif; ?>

<script src="<?=URL?>/assets/js/clipboard.min.js"></script>
<script>
    let clipboard = new ClipboardJS('.video-box-events-box');
    clipboard.on('success', (e) => {
        let t = $(e.trigger);
        t.find('.v-b-e-icon i').removeClass('fa-copy').addClass('fa-check');
        t.find('.v-b-e-text-sub').text('copied');
        setTimeout(() => {
            t.find('.v-b-e-icon i').addClass('fa-copy').removeClass('fa-check');
            t.find('.v-b-e-text-sub').text('copy');
        }, 1000);
        e.clearSelection();
    });

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
                t.removeClass('loading').removeClass('error').removeClass('action-save').addClass('action-saved');
                t.find('.v-b-a-text').text('saved');
                ic.removeClass('fa-file-upload').removeClass('fa-redo').addClass('fa-check');

                let video_index = d.message[1];
                
                // replacing download with local file ref
                let h = target.parent().find('.action-download');
                h.addClass('no-request');
                h.attr('href', '<?=URL?>/saved/videos/'+video_index+'.mp4');
            },
            error: (e) => {
                if (e.responseJSON === undefined || e.responseJSON.type === 'error') {
                    t.removeClass('loading').addClass('error');
                    t.find('.v-b-a-text').text('error, retry!');
                    ic.removeClass('fa-file-upload').addClass('fa-redo');
                }
            }
        });
    });

    $('.action-download').on('click', (e) => {
        let target = $(e.target.parentElement.parentElement.parentElement);
        if (target.find('.action-download').hasClass('no-request')) { return ; }

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
                ic.removeClass('fa-download').removeClass('fa-redo').addClass('fa-check');
                
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

    const download = (url, filename) => {
        url = "data:video/mp4,"+url;
        fetch(url).then(function(t) {
            return t.blob().then((b)=>{
                let a = document.createElement("a");
                a.href = URL.createObjectURL(b);
                a.setAttribute("download", filename);
                a.click();
            });
        });
    }

</script>
