# msg2fs

[![Build Status](https://travis-ci.org/AttestationLegale/msg2fs.svg?branch=master)](https://travis-ci.org/AttestationLegale/msg2fs)

Save message to file system.

__Install__

* Add repository to composer.json

```
"repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/AttestationLegale/msg2fs"
    }
  ]
```

* Composer require

```
composer require alg/msg2fs
```



__Exemple__

```
$message = array(
    'head' => [
      'app' => 'appName',
      'operation' => $op,
      'entity' => $entity,
      'entity_legacy' => $entity_legacy,
      'version' => 'v1',
    ],
    'prop' => [
      'delivery_mode' => 2,
    ],
    'body' => [
      'id' => 42,
      'foo' => 'bar',
    ],
  );

$exchange = 'exchangeName';
$routing_key = 'routingKey';

$msg2fs = new \Alg\Msg2fs\Msg2fs();
$msg2fs->save($message, $exchange, $routing_key);
```

__Roadmap__ 

- [ ] Bulk message
- [ ] Add more tests
