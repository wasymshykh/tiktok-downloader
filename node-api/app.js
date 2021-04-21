const express = require('express');
const TikTokScraper = require('tiktok-scraper');
const {ssstik} = require('./alternate');

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
        let failure = false;
        let url = `https://www.tiktok.com/@${user}/video/${video_id}`;
        
        try {
            const video_details = await TikTokScraper.getVideoMeta(url, {noWaterMark: true});
            if (video_details.collector.length > 0) {
                if (video_details.collector[0].videoUrlNoWaterMark !== undefined && video_details.collector[0].videoUrlNoWaterMark !== "") {
                    data = {'download': video_details.collector[0].videoUrlNoWaterMark, 'message': 'Success'};
                } else {
                    failure = true;
                }
            } else {
                failure = true;
                data = {'data': 'Unable to retrieve', 'message': 'Failure'};
            }
        } catch (error) {
            failure = true;
        }

        if (failure) {
            try {
                response = await ssstik(url);
                if (response.status) {
                    if (response.video !== undefined && response.video !== "") {
                        data = {'download': response.video, 'message': 'Success'}
                    } else {
                        data = {'data': 'Unable to retrieve', 'message': 'Failure'};
                        status = 403;
                    }
                } else {
                    data = {'data': 'Unable to retrieve', 'message': 'Failure'};
                    status = 403;
                }
            } catch (error) {
                data = {'data': 'Unable to retrieve', 'message': 'Failure'};
                status = 403;
            }
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
