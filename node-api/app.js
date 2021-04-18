const express = require('express');
const TikTokScraper = require('tiktok-scraper');

const app = express();
app.use(express.urlencoded({ extended: true }));
app.use(express.json());

const get_router = express.Router();

const isNum = (v) => {
    return /^-?\d+$/.test(v);
}

const getVideo = async (req, res) => {
    let data = {};
    let valid = true;
    let status = 200;

    let {user} = req.params;
    let {video_id} = req.params;

    if (user == undefined || user.trim() == "") {
        data = {'data': 'User cannot be empty'};
        status = 403;
        valid = false;
    }
    if (video_id == undefined || video_id.trim() == "") {
        data = {'data': 'Video ID cannot be empty', 'message': 'Invalid Request'};
        status = 403;
        valid = false;
    } else if (!isNum(video_id)) {
        data = {'data': 'Video ID can only be a number', 'message': 'Invalid Request'};
        status = 403;
        valid = false;
    }

    if (valid) {
        try {
            const video_details = await TikTokScraper.getVideoMeta(`https://www.tiktok.com/@${user}/video/${video_id}`, {noWaterMark: true});
            data = video_details;
        } catch (error) {
            data = {'data': 'Unable to retrieve', 'message': 'Failure'};
            status = 403;
        }
    }
    
    return res.status(status).json(data);
}


get_router.get('/:user/:video_id', getVideo);
app.use('/get', get_router);


app.use((req, res, next) => {
    return res.status(400).json({'data': 'Request parameter is incorrect', 'message': 'Invalid Request'});
})

app.listen(3000, (err) => {
    if (err) throw err;
    console.log('Server running in http://127.0.0.1:3000');
})
