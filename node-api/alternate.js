/*
    Author: @MRHRTZ
    Modified by me.
*/

const { default: Axios } = require('axios')
const cheerio = require('cheerio')
const qs = require('qs')

function ssstik(url) {
     return new Promise((resolve, reject) => {
          const SSS = 'https://ssstik.io'
          Axios.request({
               url: SSS,
               method: 'get',
               headers: {
                    'cookie': 'PHPSESSID=gse3nalsujsrkqvuhuk3jb31ik; __cfduid=dc7b75ce1e12813b50a0dfc692f17ab991618751521; __cflb=02DiuEcwseaiqqyPC5reASCyLDygUABtEhAz2neZ1SHAs',
                    'user-agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.90 Safari/537.36',
                    'sec-ch-ua': '"Google Chrome";v="89", "Chromium";v="89", ";Not A Brand";v="99"'
               }
          }).then(({ data }) => {
                const $ = cheerio.load(data)
                const urlPost = $('form[data-hx-target="#target"]').attr('data-hx-post')
                const tokenJSON = $('form[data-hx-target="#target"]').attr('include-vals')
                const tt = tokenJSON.replace(/'/g, '').replace('tt:', '').split(',')[0]
                const ts = tokenJSON.split('ts:')[1]
                const config = {
                    headers: {
                        'content-type': 'application/x-www-form-urlencoded; charset=UTF-8',
                        'cookie': 'PHPSESSID=gse3nalsujsrkqvuhuk3jb31ik; __cfduid=dc7b75ce1e12813b50a0dfc692f17ab991618751521; __cflb=02DiuEcwseaiqqyPC5reASCyLDygUABtEhAz2neZ1SHAs',
                        'sec-ch-ua': '"Google Chrome";v="89", "Chromium";v="89", ";Not A Brand";v="99"',
                        'user-agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.90 Safari/537.36',
                    },
                    dataPost: {
                        'id': url,
                        'locale': 'en',
                        'tt': tt,
                        'ts': ts
                    }
                }

                Axios.post(SSS + urlPost, qs.stringify(config.dataPost), { headers: config.headers }).then(({ data }) => {
                    const $ = cheerio.load(data)
                    const result = {
                        status: true,
                        text: $('div > p').text(),
                        video: SSS + $('div > a.without_watermark').attr('href'),
                        video_original: $('div > a.without_watermark_direct').attr('href'),
                        music: $('div > a.music').attr('href')
                    }
                    if ($('div > a.without_watermark_direct').attr('href') !== undefined) {
                        resolve(result)
                    } else {
                        reject({ status: false, message: 'Unable to retrieve' })
                    }
                }).catch(reject)
            }).catch(reject)
     })
}

module.exports.ssstik = ssstik