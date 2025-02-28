<?php

namespace Swark\DataModel\InformationTechnology\Domain\Model\Network;

/**
 * @see https://www.ionos.com/digitalguide/hosting/technical-matters/dns-records/
 */
enum DnsRecordType: string
{
    case A = 'a';
    case AAAA = 'aaaa';
    case SOA = 'soa';
    case CNAME = 'cname';
    case MX = 'mx';
    case NS = 'ns';
    case PTR = 'ptr';
    case TXT = 'txt';
    case SRV = 'srv';
    case LOC = 'loc';
    case RRSIG = 'rrsig';
    case KX = 'kx';
    case CERT = 'cert';
    case DS = 'ds';
    case APL = 'apl';
    case SSHFP = 'sshfp';
    case IPSECKEY = 'ipseckey';
    case NSEC = 'nsec';
    case DNSKEY = 'dnskey';
    case DHCID = 'dhcid';
    case TLSA = 'tlsa';
    case SMIMEA = 'smimea';
    case HIP = 'hip';
    case CDS = 'cds';
    case CDNSKEY = 'cdnskey';
    case OPENPGPKEY = 'openpgpkey';
    case EUI48 = 'eui48';
    case EUI64 = 'eui64';
    case TKEY = 'tkey';
    case TSIG = 'tsig';
    case URI = 'uri';
    case CAA = 'caa';
    case TA = 'ta';
    case TLV = 'tlv';

    static function toMap(): array
    {
        return collect(static::cases())->map(fn($item) => [\Illuminate\Support\Str::lower($item->value) => $item->value])->toArray();
    }
}
