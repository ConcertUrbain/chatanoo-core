echo "Zipping sources"
mkdir build
zip -r build/application.zip . -x *.git* build\*
cd lambdas/aws-cloudformation-chatanoo-apikey
npm install
zip -r ../../build/chatanoo-apikey.zip .
cd ../..

echo "Deploy $TRAVIS_TAG version to S3"
aws s3 cp infra/core.cfn.yml s3://chatanoo-deployments-eu-west-1/infra/core/$TRAVIS_TAG.cfn.yml
aws s3 cp build/application.zip s3://chatanoo-deployments-eu-west-1/core/application/$TRAVIS_TAG.zip
aws s3 cp build/chatanoo-apikey.zip s3://chatanoo-deployments-eu-west-1/core/chatanoo-apikey/$TRAVIS_TAG.zip
aws s3 cp Database/structure.sql s3://chatanoo-deployments-eu-west-1/core/database/$TRAVIS_TAG.sql

echo "Upload latest"
aws s3api put-object \
  --bucket chatanoo-deployments-eu-west-1 \
  --key infra/core/latest.cfn.yml \
  --website-redirect-location /infra/core/$TRAVIS_TAG.cfn.yml
aws s3api put-object \
  --bucket chatanoo-deployments-eu-west-1 \
  --key core/application/latest.zip \
  --website-redirect-location /core/application/$TRAVIS_TAG.zip
aws s3api put-object \
  --bucket chatanoo-deployments-eu-west-1 \
  --key core/chatanoo-apikey/latest.zip \
  --website-redirect-location /core/chatanoo-apikey/$TRAVIS_TAG.zip
aws s3api put-object \
  --bucket chatanoo-deployments-eu-west-1 \
  --key core/database/latest.sql \
  --website-redirect-location /core/database/$TRAVIS_TAG.sql