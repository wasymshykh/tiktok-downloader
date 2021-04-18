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
        <div class="video-box" data-videoid="<?=$video->id?>" data-username="<?=$video->author->uniqueId?>">
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
                    <div class="video-box-action action-save">
                        <div class="v-b-a-icon"><i class="fas fa-file-upload"></i></div>
                        <div class="v-b-a-text">save</div>
                    </div>
                    <div class="video-box-action action-download">
                        <div class="v-b-a-icon"><i class="fas fa-download"></i></div>
                        <div class="v-b-a-text">download</div>
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
