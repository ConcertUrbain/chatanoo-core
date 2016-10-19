var CfnLambda = require('cfn-lambda');
var AWS = require('aws-sdk');

var ChatanooApiKeyLib = require('./lib/chatanoo-apikey');

function ChatanooApiKeyHandler(event, context) {
  var ChatanooApiKey = CfnLambda({
    Create: ChatanooApiKeyLib.Create,
    Update: ChatanooApiKeyLib.Update,
    Delete: ChatanooApiKeyLib.Delete,
    SchemaPath: [__dirname, 'src', 'schema.json']
  });
  // Not sure if there's a better way to do this...
  AWS.config.region = currentRegion(context);

  return ChatanooApiKey(event, context);
}

function currentRegion(context) {
  return context.invokedFunctionArn.match(/^arn:aws:lambda:(\w+-\w+-\d+):/)[1];
}

exports.handler = ChatanooApiKeyHandler;
