from flask import Flask 
from flask_cors import CORS
from .api.routes import api 
from .site.routes import site 

def create_app():
    app = Flask(__name__)
    CORS(app)
    app.register_blueprint(api)
    app.register_blueprint(site)
    
    if __name__ == '__main__':
        app.run()

    return app