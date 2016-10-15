echo "Zipping sources"
mkdir build
git archive --format=zip HEAD > build/application.zip

echo "Deploy $TRAVIS_TAG version to S3"
aws s3 cp infra/core.cfn.yml s3://chatanoo-deployment.eu-west-1/infra/core/$TRAVIS_TAG.cfn.yml
aws s3 cp build/application.zip s3://chatanoo-deployment.eu-west-1/core/application/$TRAVIS_TAG.zip
aws s3 cp Database/structure.sql s3://chatanoo-deployment.eu-west-1/core/database/$TRAVIS_TAG.sql

echo "Upload latest"
aws s3api put-object \
  --bucket chatanoo-deployment.eu-west-1 \
  --key infra/core/latest.cfn.yml \
  --website-redirect-location /infra/core/$TRAVIS_TAG.cfn.yml
aws s3api put-object \
  --bucket chatanoo-deployment.eu-west-1 \
  --key core/application/latest.zip \
  --website-redirect-location /core/application/$TRAVIS_TAG.zip
aws s3api put-object \
  --bucket chatanoo-deployment.eu-west-1 \
  --key core/database/latest.sql \
  --website-redirect-location /core/database/$TRAVIS_TAG.sql
