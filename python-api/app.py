from gevent import monkey
monkey.patch_all()
from flask import Flask
from flask_restful import abort, Api, Resource
from TikTokApi import TikTokApi

errors = {
    'ResourceDoesNotExist': {
        'data': "Request parameter is incorrect",
        'status': 400,
        'message': "Invalid Request"
    },
    'NotFound': {
        'data': "Request parameter is incorrect",
        'status': 404,
        'message': "Invalid Request"
    }
}

app = Flask(__name__)
server = Api(app, errors=errors, catch_all_404s=True)


# this token need to be updated after some hours
VERIFY_TOKEN = "YOUR_TOKEN"
LIMIT_PER_PAGE = 30
tiktok = TikTokApi.get_instance(custom_verifyFp=VERIFY_TOKEN, use_test_endpoints=True, proxy="YOUR_PROXY")

def no_exists (data, status=403, message = ""):
    if not message:
        message = "Invalid Request"
    abort(status, message=message, data=data)

class Hashtag (Resource):
    def get(self, page, tag):
        if page < 1:
            no_exists(message="Invalid Request", data="Page must have minimum 1 value")
        if page > 10:
            no_exists(message="Limit Exceeded", data="More than 10 pages are not allowed")
        if not tag:
            no_exists(message="Empty", data="Tag cannot be empty string")
        if not tag.isalnum():
            no_exists(message="Invalid String", data="Tag can only contain alphabets and numbers")
        
        try:
            content = tiktok.by_hashtag(count=LIMIT_PER_PAGE, offset=LIMIT_PER_PAGE*(page-1), hashtag=tag)
        except:
            no_exists(message="Failure", data="Unable to retrieve results or data not found")

        return {'message': 'Success', 'data': content}

class Profile (Resource):
    def get(self, user):
        if not user:
            no_exists(message="Empty", data="Username cannot be empty string")
        
        try:
            content = tiktok.by_username(user, count=LIMIT_PER_PAGE)
        except:
            no_exists(message="Failure", data="Unable to retrieve results or data not found")

        return {'message': 'Success', 'data': content}

class Video (Resource):
    def get(self, user, id):
        url = "https://www.tiktok.com/@"+user+"/video/"+id
        try:
            content = tiktok.get_tiktok_by_url(url)
        except Exception as e:
            no_exists(message="Failure", data=str(e))
        return {'message': 'Success', 'data': content}

server.add_resource(Hashtag, '/hashtag/<int:page>/<string:tag>')
server.add_resource(Profile, '/profile/<string:user>')
server.add_resource(Video, '/video/<string:user>/<string:id>')

if __name__ == '__main__':
    app.run(threaded=False)
    # In production add host parameter host='0.0.0.0'
