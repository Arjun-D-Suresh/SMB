from flask import Blueprint, request
from .controller.iepf2UploadController import IEPF2Controller
from dotenv import load_dotenv
import os

load_dotenv()

UPLOAD_PATH = os.getenv("UPLOAD_PATH")

api = Blueprint('api', __name__, url_prefix='/api')
  
iepf2controller = IEPF2Controller()

@api.route('/uploader', methods = ['POST'])
def upload_file():
    file_paths = []
    if request.method == 'POST':
        file_type = request.form.get("fileType")
        files = request.files.getlist("file")
        print("files => ", files, "\nfile type =>", file_type)
        
        if files[0].filename == '':
            result = { "data": [], "message": "No file selected"}, 400
            return result

        if file_type == 'IEPF2':
            try:
                for f in files: 
                    f.save(UPLOAD_PATH + file_type.lower() + "/" + f.filename)
                    file_paths.append(UPLOAD_PATH + file_type.lower() + "/" + f.filename)
                result = iepf2controller.insert_excel_data(file_paths, file_type)
            except Exception as e:
                result = { "data": [], "message": str(e)}, 500
        else:
            result = { "data": [], "message": "wrong file type"}, 400

        return result

@api.route('/multiple-dividend', methods = ['POST'])
def multiple_dividend():

    cin = request.json['cin']
    log_id = request.json['log_id']
    xfer_date = request.json['xfer_date']
    division = request.json['division']
    dividend_id = request.json['dividend_id']
    return iepf2controller.insert_multiple_dividend(cin,log_id,xfer_date,division,dividend_id)


@api.route('/get-multidividend-data', methods = ['POST'])
def get_multidividend():
    cin = request.json['cin']
    security_code = request.json['security_code']
    log_id = request.json['log_id']
    xfer_date = request.json['xfer_date']
    division = request.json['division']
    skip = request.json['skip']
    take = request.json['take']

    result = iepf2controller.get_multiple_dividend(cin, security_code, log_id, xfer_date, division, skip, take)
    return result