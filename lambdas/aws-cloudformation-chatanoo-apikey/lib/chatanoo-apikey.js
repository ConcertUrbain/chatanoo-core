var AWS = require('aws-sdk');
var s3 = new AWS.S3();
var mysql = require('mysql');
var sha1 = require('sha1');
var generatePassword = require('password-generator');

var env;
function loadEnvironement(environment, callback) {
  if (env) return callback(null, env);

  s3.getObject(environment, function(err, data) {
    if (err) return callback('Load Environement: ' + err);
    env = JSON.parse(data.Body.toString());
    callback(null, env)
  });
}

function getConnection(env, multi) {
  return mysql.createConnection({
    host: env.DBHost,
    user: env.DBUser,
    password: env.DBPass,
    database: env.DBName,
    port: env.DBPort,
    multipleStatements: multi ? true : false
  });
}

function addKey(file, key, name, session, callback) {
  s3.getObject(file, function(err, data) {
    if (err) return callback('Load ApiKeys: ' + err);
    var content = JSON.parse(data.Body.toString());

    content.api_key.push({
      key: key,
      name: name,
      session: "" + session
    });
    var params = {
      Bucket: file.Bucket,
      Key: file.Key,
      ACL: 'public-read',
      ContentType: 'application/json',
      Body: JSON.stringify(content)
    }
    s3.putObject(params, callback);
  });
}

function removeKey(file, key, callback) {
  s3.getObject(file, function(err, data) {
    if (err) return callback('Load ApiKeys: ' + err);
    var content = JSON.parse(data.Body.toString());

    var toDelete = -1;
    for (var i = 0; i < content.api_key.length; i++) {
      if (content.api_key[i].key == key) toDelete = i;
    }
    delete content.api_key[toDelete];
    var params = {
      Bucket: file.Bucket,
      Key: file.Key,
      ACL: 'public-read',
      ContentType: 'application/json',
      Body: JSON.stringify(content)
    }
    s3.putObject(params, callback);
  });
}

var Create = function(params, reply) {
  loadEnvironement(params.Environment, function(err, env) {
    if (err) return reply(err);

    var connection = getConnection(env);
    var user = 'admin';
    var password = generatePassword();
    var api_key = generatePassword(32, false);

    // Create Session
    var sql = "INSERT INTO sessions SET ?;";
    var p = {
      title: params.Site,
      description: params.Host
    }
    connection.query(sql, p, function(err, result1) {
      if (err) return reply("Create Session: " + err);

      // Create Admin User
      sql = "INSERT INTO users SET ?;";
      var p = {
        pseudo: user,
        password: sha1(password),
        role: 'admin',
        sessions_id: result1.insertId
      }
      connection.query(sql, p, function(err, result2) {
        if (err) return reply("Create Admin User: " + err);

        // Create ApiKey
        sql = "INSERT INTO api_keys SET ?;";
        var p = {
          api_key: api_key,
          host: params.Host,
          site: params.Site,
          sessions_id: result1.insertId,
          users_id: result2.insertId
        }
        connection.query(sql, p, function(err, result3) {
          if (err) return reply("Create ApiKey: " + err);

          var file = {
            Bucket: params.Environment.Bucket,
            Key: env.ApiKeysObject
          };
          addKey(file, api_key, params.Site, result1.insertId, function(err) {
            if (err) return reply("AddKey: " + err);
            reply(null, api_key, { User: user, Password: password });
          });
        });
      });
    });
  });
};

var Update = function(physicalId, params, oldParams, reply) {
  if (params.Host == oldParams.Host && params.Site == oldParams.Site)
    reply(null, physicalId);

  loadEnvironement(params.Environment, function(err, env) {
    if (err) return reply(err);

    var connection = getConnection(env);
    var sql = "UPDATE api_keys SET host = ?, site = ? WHERE api_key = ?";
    var p = [params.Host, params.Site, physicalId];
    connection.query(sql, p, function(err, result) {
      if (err) return reply("Update ApiKey: " + err);

      reply(null, physicalId);
    });
  });
};

var Delete = function(physicalId, params, reply) {
  loadEnvironement(params.Environment, function(err, env) {
    if (err) return reply(err);

    var connection = getConnection(env, true);

    var sql = "SELECT * FROM api_keys WHERE api_key = " + connection.escape(physicalId) + ";";
    connection.query(sql, function(err, results) {
      if (err) return reply("Get ApiKey row: ", err);

      sql =  "DELETE FROM users WHERE id = " + connection.escape(results[0].users_id) + ";";
      sql += "DELETE FROM sessions WHERE id = " + connection.escape(results[0].sessions_id) + ";";
      sql += "DELETE FROM api_keys WHERE id = " + connection.escape(results[0].id) + ";";
      connection.query(sql, function(err, results) {
        if (err) return reply(err);

        var file = {
          Bucket: params.Environment.Bucket,
          Key: env.ApiKeysObject
        };
        removeKey(file, physicalId, function(err) {
          if (err) return reply("removeKey: " + err);
          reply(null, physicalId);
        });
      });
    });

  });
};

exports.Create = Create;
exports.Update = Update;
exports.Delete = Delete;
